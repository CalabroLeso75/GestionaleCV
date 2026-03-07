<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;
use ZipArchive;

class FileManagerController extends Controller
{
    /** Allowed root — everything must stay under here */
    private function root(): string
    {
        return realpath(base_path());
    }

    /**
     * Resolve and validate a user-supplied path.
     * Returns the real absolute path or throws 403.
     */
    private function safePath(string $relativePath): string
    {
        $root = $this->root();
        // Strip leading slash and resolve
        $clean = ltrim(str_replace(['..', '\\'], ['', '/'], $relativePath), '/');
        $abs   = $root . DIRECTORY_SEPARATOR . $clean;
        $real  = realpath($abs);

        // Allow non-existent paths for mkdir/rename targets, but parent must exist
        if ($real === false) {
            // check parent
            $parent = realpath(dirname($abs));
            if ($parent === false || strpos($parent . DIRECTORY_SEPARATOR, $root . DIRECTORY_SEPARATOR) !== 0) {
                abort(403, 'Percorso non consentito.');
            }
            return $abs;
        }

        if (strpos($real . DIRECTORY_SEPARATOR, $root . DIRECTORY_SEPARATOR) !== 0 && $real !== $root) {
            abort(403, 'Percorso non consentito.');
        }
        return $real;
    }

    /** Convert absolute path to relative (for client display). */
    private function toRelative(string $abs): string
    {
        $root = $this->root();
        $rel  = str_replace($root, '', $abs);
        return '/' . ltrim(str_replace('\\', '/', $rel), '/');
    }

    // ── View ─────────────────────────────────────────────────────────

    public function index()
    {
        return view('admin.filemanager.index', ['root' => $this->toRelative($this->root())]);
    }

    // ── API: List directory ──────────────────────────────────────────

    public function ls(Request $request)
    {
        $path = $this->safePath($request->input('path', ''));
        if (!is_dir($path)) {
            return response()->json(['error' => 'Non è una directory.'], 400);
        }

        $items = [];
        $scan  = array_diff(scandir($path), ['.', '..']);

        foreach ($scan as $name) {
            $full = $path . DIRECTORY_SEPARATOR . $name;
            $stat = stat($full);
            $items[] = [
                'name'       => $name,
                'path'       => $this->toRelative($full),
                'type'       => is_dir($full) ? 'dir' : 'file',
                'size'       => is_file($full) ? $stat['size'] : null,
                'modified'   => $stat['mtime'],
                'readable'   => is_readable($full),
                'writable'   => is_writable($full),
                'perms'      => substr(sprintf('%o', fileperms($full)), -4),
                'extension'  => is_file($full) ? strtolower(pathinfo($name, PATHINFO_EXTENSION)) : null,
            ];
        }

        // Dirs first, then files; both alphabetical
        usort($items, fn($a, $b) => $a['type'] !== $b['type']
            ? ($a['type'] === 'dir' ? -1 : 1)
            : strcasecmp($a['name'], $b['name'])
        );

        return response()->json([
            'path'  => $this->toRelative($path),
            'items' => $items,
        ]);
    }

    /** Return only subdirectories for tree sidebar */
    public function tree(Request $request)
    {
        $path = $this->safePath($request->input('path', ''));
        if (!is_dir($path)) return response()->json([]);

        $dirs = [];
        foreach (array_diff(scandir($path), ['.', '..']) as $name) {
            $full = $path . DIRECTORY_SEPARATOR . $name;
            if (is_dir($full)) {
                $dirs[] = [
                    'name'     => $name,
                    'path'     => $this->toRelative($full),
                    'hasChildren' => count(array_filter(array_diff(scandir($full), ['.', '..']),
                        fn($n) => is_dir($full . DIRECTORY_SEPARATOR . $n))) > 0,
                ];
            }
        }
        return response()->json($dirs);
    }

    // ── API: Create directory ────────────────────────────────────────

    public function mkdir(Request $request)
    {
        $request->validate(['path' => 'required', 'name' => 'required|regex:/^[^\\/\\0]+$/']);
        $parent  = $this->safePath($request->input('path'));
        $newDir  = $parent . DIRECTORY_SEPARATOR . $request->input('name');

        if (file_exists($newDir)) {
            return response()->json(['error' => 'Esiste già.'], 409);
        }
        mkdir($newDir, 0755, true);
        return response()->json(['success' => true, 'path' => $this->toRelative($newDir)]);
    }

    // ── API: Rename ──────────────────────────────────────────────────

    public function rename(Request $request)
    {
        $request->validate(['path' => 'required', 'name' => 'required|regex:/^[^\\/\\0]+$/']);
        $src    = $this->safePath($request->input('path'));
        $dst    = dirname($src) . DIRECTORY_SEPARATOR . $request->input('name');
        $dstAbs = $this->safePath($this->toRelative(dirname($src)) . '/' . $request->input('name'));

        if (file_exists($dstAbs)) {
            return response()->json(['error' => 'Nome già in uso.'], 409);
        }
        rename($src, $dst);
        return response()->json(['success' => true, 'path' => $this->toRelative($dst)]);
    }

    // ── API: Delete ──────────────────────────────────────────────────

    public function delete(Request $request)
    {
        $request->validate(['paths' => 'required|array']);
        foreach ($request->input('paths') as $p) {
            $abs = $this->safePath($p);
            if (is_dir($abs)) {
                $this->deleteDir($abs);
            } elseif (is_file($abs)) {
                unlink($abs);
            }
        }
        return response()->json(['success' => true]);
    }

    private function deleteDir(string $dir): void
    {
        foreach (array_diff(scandir($dir), ['.', '..']) as $item) {
            $full = $dir . DIRECTORY_SEPARATOR . $item;
            is_dir($full) ? $this->deleteDir($full) : unlink($full);
        }
        rmdir($dir);
    }

    // ── API: Copy ────────────────────────────────────────────────────

    public function copy(Request $request)
    {
        $request->validate(['sources' => 'required|array', 'destination' => 'required']);
        $dst = $this->safePath($request->input('destination'));
        if (!is_dir($dst)) return response()->json(['error' => 'Destinazione non è una cartella.'], 400);

        foreach ($request->input('sources') as $src) {
            $absSrc = $this->safePath($src);
            $name   = basename($absSrc);
            $absDst = $dst . DIRECTORY_SEPARATOR . $name;
            if (is_dir($absSrc)) {
                $this->copyDir($absSrc, $absDst);
            } else {
                copy($absSrc, $absDst);
            }
        }
        return response()->json(['success' => true]);
    }

    private function copyDir(string $src, string $dst): void
    {
        mkdir($dst, 0755, true);
        foreach (array_diff(scandir($src), ['.', '..']) as $item) {
            $s = $src . DIRECTORY_SEPARATOR . $item;
            $d = $dst . DIRECTORY_SEPARATOR . $item;
            is_dir($s) ? $this->copyDir($s, $d) : copy($s, $d);
        }
    }

    // ── API: Move ────────────────────────────────────────────────────

    public function move(Request $request)
    {
        $request->validate(['sources' => 'required|array', 'destination' => 'required']);
        $dst = $this->safePath($request->input('destination'));
        if (!is_dir($dst)) return response()->json(['error' => 'Destinazione non è una cartella.'], 400);

        foreach ($request->input('sources') as $src) {
            $absSrc = $this->safePath($src);
            rename($absSrc, $dst . DIRECTORY_SEPARATOR . basename($absSrc));
        }
        return response()->json(['success' => true]);
    }

    // ── API: Download ────────────────────────────────────────────────

    public function download(Request $request)
    {
        $path = $this->safePath($request->input('path'));
        if (!is_file($path)) abort(404);
        return response()->download($path);
    }

    // ── API: Upload ──────────────────────────────────────────────────

    public function upload(Request $request)
    {
        $request->validate(['path' => 'required', 'files' => 'required|array', 'files.*' => 'file']);
        $dir = $this->safePath($request->input('path'));
        if (!is_dir($dir)) return response()->json(['error' => 'Cartella non valida.'], 400);

        $uploaded = [];
        foreach ($request->file('files') as $file) {
            $name = $file->getClientOriginalName();
            $file->move($dir, $name);
            $uploaded[] = $name;
        }
        return response()->json(['success' => true, 'uploaded' => $uploaded]);
    }

    // ── API: Read file content ───────────────────────────────────────

    public function readFile(Request $request)
    {
        $path = $this->safePath($request->input('path'));
        if (!is_file($path)) return response()->json(['error' => 'Non è un file.'], 400);
        if (filesize($path) > 2 * 1024 * 1024) {
            return response()->json(['error' => 'File troppo grande per l\'editor (max 2MB).'], 400);
        }
        $content = file_get_contents($path);
        return response()->json(['content' => $content, 'path' => $this->toRelative($path)]);
    }

    // ── API: Write file content ──────────────────────────────────────

    public function writeFile(Request $request)
    {
        $request->validate(['path' => 'required', 'content' => 'nullable|string']);
        $path = $this->safePath($request->input('path'));
        file_put_contents($path, $request->input('content', ''));
        return response()->json(['success' => true]);
    }

    // ── API: Execute command (whitelist) ─────────────────────────────

    public function exec(Request $request)
    {
        $request->validate(['command' => 'required|string|max:500', 'cwd' => 'nullable|string']);

        $cmd  = trim($request->input('command'));
        $cwd  = $request->input('cwd') ? $this->safePath($request->input('cwd')) : $this->root();

        // Whitelist: only allow specific base commands
        $allowed = ['ls', 'dir', 'pwd', 'whoami', 'php', 'composer', 'git', 'tail', 'cat', 'find', 'df', 'du', 'echo'];
        $base = strtolower(explode(' ', $cmd)[0]);
        if (!in_array($base, $allowed)) {
            return response()->json(['output' => "❌ Comando '$base' non consentito.\nConsentiti: " . implode(', ', $allowed)]);
        }

        // Extra safety: no shell metacharacters
        if (preg_match('/[;&|`$(){}<>!]/', $cmd)) {
            return response()->json(['output' => '❌ Caratteri speciali non consentiti nel comando.']);
        }

        $descriptors = [1 => ['pipe', 'w'], 2 => ['pipe', 'w']];
        $process = proc_open($cmd, $descriptors, $pipes, $cwd);

        if (!is_resource($process)) {
            return response()->json(['output' => '❌ Impossibile eseguire il comando.']);
        }

        $stdout = stream_get_contents($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[1]); fclose($pipes[2]);
        proc_close($process);

        $output = $stdout . ($stderr ? "\n[STDERR]: $stderr" : '');
        return response()->json(['output' => $output ?: '(nessun output)']);
    }

    // ── API: Zip ─────────────────────────────────────────────────────

    public function zip(Request $request)
    {
        $request->validate(['paths' => 'required|array', 'destination' => 'required|string']);
        $paths = $request->input('paths');
        if (empty($paths)) {
            return response()->json(['error' => 'Nessun file selezionato.'], 400);
        }

        // Determine parent dir from first file to save the zip
        $firstAbs = $this->safePath($paths[0]);
        $dir = is_dir($firstAbs) ? dirname($firstAbs) : dirname($firstAbs);
        
        $zipName = basename($request->input('destination'));
        if (!str_ends_with($zipName, '.zip')) $zipName .= '.zip';
        
        $zipAbs = $dir . DIRECTORY_SEPARATOR . $zipName;

        $zip = new ZipArchive();
        if ($zip->open($zipAbs, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return response()->json(['error' => 'Impossibile creare lo zip.'], 500);
        }

        foreach ($paths as $p) {
            $abs = $this->safePath($p);
            $relName = basename($abs);
            if (is_file($abs)) {
                $zip->addFile($abs, $relName);
            } elseif (is_dir($abs)) {
                $this->addDirToZip($abs, $zip, $relName);
            }
        }
        $zip->close();
        
        return response()->json(['success' => true, 'path' => $this->toRelative($zipAbs)]);
    }

    private function addDirToZip(string $dir, ZipArchive $zip, string $basePrefix)
    {
        $zip->addEmptyDir($basePrefix);
        foreach (array_diff(scandir($dir), ['.', '..']) as $item) {
            $full = $dir . DIRECTORY_SEPARATOR . $item;
            $localName = $basePrefix . '/' . $item;
            if (is_file($full)) {
                $zip->addFile($full, $localName);
            } elseif (is_dir($full)) {
                $this->addDirToZip($full, $zip, $localName);
            }
        }
    }

    // ── API: Unzip ───────────────────────────────────────────────────

    public function unzip(Request $request)
    {
        $request->validate(['path' => 'required|string']);
        $abs = $this->safePath($request->input('path'));
        if (!is_file($abs) || !str_ends_with(strtolower($abs), '.zip')) {
            return response()->json(['error' => 'File zip non valido.'], 400);
        }

        $dir = dirname($abs);
        $folderName = pathinfo($abs, PATHINFO_FILENAME);
        $extractTo = $dir . DIRECTORY_SEPARATOR . $folderName;

        // Ensure unique folder name
        $i = 1;
        while (file_exists($extractTo)) {
            $extractTo = $dir . DIRECTORY_SEPARATOR . $folderName . " ($i)";
            $i++;
        }

        $zip = new ZipArchive();
        if ($zip->open($abs) === true) {
            mkdir($extractTo, 0755, true);
            $zip->extractTo($extractTo);
            $zip->close();
            return response()->json(['success' => true, 'path' => $this->toRelative($extractTo)]);
        }
        
        return response()->json(['error' => 'Impossibile estrarre lo zip.'], 500);
    }

    // ── API: Chmod ───────────────────────────────────────────────────

    public function chmod(Request $request)
    {
        $request->validate([
            'path' => 'required|string',
            'permissions' => 'required|string|regex:/^[0-7]{3,4}$/'
        ]);
        
        $abs = $this->safePath($request->input('path'));
        $perms = octdec($request->input('permissions'));
        
        if (@chmod($abs, $perms)) {
            return response()->json(['success' => true]);
        }
        return response()->json(['error' => 'Impossibile cambiare i permessi.'], 500);
    }

    // ── API: Search ──────────────────────────────────────────────────

    public function search(Request $request)
    {
        $request->validate([
            'path' => 'required|string',
            'query' => 'required|string|min:2'
        ]);

        $dir = $this->safePath($request->input('path'));
        if (!is_dir($dir)) return response()->json(['error' => 'Cartella non valida.'], 400);

        $q = $request->input('query');
        $results = [];
        
        // Simple PHP recursive search for filename match
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        $count = 0;
        foreach ($iterator as $fileInfo) {
            if (stripos($fileInfo->getFilename(), $q) !== false) {
                $full = $fileInfo->getRealPath();
                $stat = stat($full);
                $results[] = [
                    'name'       => $fileInfo->getFilename(),
                    'path'       => $this->toRelative($full),
                    'type'       => $fileInfo->isDir() ? 'dir' : 'file',
                    'size'       => $fileInfo->isFile() ? $stat['size'] : null,
                    'modified'   => $stat['mtime'],
                    'readable'   => is_readable($full),
                    'writable'   => is_writable($full),
                    'perms'      => substr(sprintf('%o', fileperms($full)), -4),
                    'extension'  => $fileInfo->isFile() ? strtolower(pathinfo($fileInfo->getFilename(), PATHINFO_EXTENSION)) : null,
                ];
                $count++;
                if ($count >= 100) break; // Limit results
            }
        }

        return response()->json(['items' => $results]);
    }
}

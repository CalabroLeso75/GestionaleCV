# Standard di Documentazione

Come da requisiti di progetto, ogni file significativo del codice sorgente deve avere un corrispettivo file di documentazione `.md`.

## Struttura

La documentazione seguirà la struttura delle cartelle del progetto.
Esempio:

- `app/Http/Controllers/UserController.php` -> `docs/app/Http/Controllers/UserController.md`

## Contenuto

Ogni file `.md` deve contenere:

1. **Scopo**: A cosa serve il file/classe.
2. **Funzionamento**: Logica principale.
3. **Relazioni**: Quali altri componenti utilizza o da cui è utilizzato.
4. **Note Sviluppatore**: Istruzioni per la manutenzione futura.

## Esempio per `UserController.md`

```markdown
# UserController

## Scopo
Gestisce le operazioni CRUD per gli utenti del gestionale.

## Funzionamento
- `index()`: Mostra la lista utenti.
- `store()`: Salva un nuovo utente dopo validazione.
...
```

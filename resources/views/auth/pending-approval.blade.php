<x-guest-layout>
    <div class="text-center">
        <h2 class="h4 text-success mb-4">Email Verificata con Successo!</h2>
        
        <p class="mb-4">
            La tua registrazione è stata completata.
        </p>
        <p class="alert alert-warning">
            <strong>Attenzione:</strong> Il tuo account è attualmente in stato 
            <span style="color: #d35400; font-weight: bold; text-transform: uppercase;">IN ATTESA DI APPROVAZIONE</span>.
            <br><br>
            Un amministratore deve abilitare il tuo accesso. Riceverai una notifica quando il tuo account sarà attivo.
        </p>

        <a href="{{ route('welcome') }}" class="btn btn-primary mt-3">Torna alla Home</a>
    </div>
</x-guest-layout>

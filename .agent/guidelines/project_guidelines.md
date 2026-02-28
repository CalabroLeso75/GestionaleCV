# Linee Guida di Progetto: Logging Obbligatorio

Per garantire la tracciabilità delle azioni e la sicurezza del sistema, è **tassativo** implementare il logging delle attività per ogni nuova funzionalità o pagina sviluppata.

## Standard di Implementazione

### Utilizzo di ActivityLogger

Tutte le azioni significative effettuate dagli utenti devono essere registrate utilizzando la classe `App\Services\ActivityLogger`.

```php
use App\Services\ActivityLogger;

// Esempio di utilizzo in un Controller
ActivityLogger::log(
    'nome_azione',      // Stringa identificativa dell'azione (es. 'calculate_cf')
    'NomeModello',      // Opzionale: Nome del modello coinvolto
    $id,                // Opzionale: ID del record coinvolto
    'Dettaglio azione'  // Descrizione leggibile dell'azione eseguita
);
```

### Azioni da Loggare

Devono essere loggate obbligatoriamente le seguenti operazioni:

- **Calcoli e Generazioni:** Qualsiasi azione che generi dati calcolati (es. Codice Fiscale).
- **Modifiche (CRUD):** Creazione, aggiornamento ed eliminazione di record.
- **Accessi e Sicurezza:** Login, logout, cambi password, assegnazione ruoli.
- **Configurazioni:** Modifiche alle impostazioni del sito o server (SMTP, etc.).

### Formato dei Dettagli

Il campo `details` deve contenere una descrizione chiara che permetta a un amministratore di capire esattamente cosa è successo, includendo parametri rilevanti (es. "Generato CF per Mario Rossi").

## Prima installazione su server
- Aggiungere un file .env per l'ambiente di produzione
- Creare le chiavi per passport: `artisan passport:keys`
- Crere il client oauth: `artisan passport:client --password`
- Aggiornare il file .env con l'id e la chiave del client oauth

## Dopo ogni deploy
- Installazione delle dipendenze: `php composer.phar install`
- Link della directory di storage: `rm storage && ln -sT <directory_storage> storage`
- Migrazione con seed dei dati principali: `php artisan migrate --force --seed`

## Per far funzionare l'import di dati da analytics
- copiare `analytics_credentials.json` nella directory di storage
- lanciare il primo import: `php artisan analytics:import --days:15`

## Per funzionare la generazione delle sitemap
- Assicurarsi che PHP abbia `memory_limit` a `512M` (per la generazione delle sitemap)
- lanciare la prima generazione: `php artisan sitemap:generate`

## Come usare gli importer:
TODO: ...

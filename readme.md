# Configurazione di un nuovo server

## Attivare le estensioni PHP

Abbiamo bisogno delle estensioni Locale e FileInfo, rispettivamente per calcolare la lingua in `<html lang="[lingua]">` partendo dal locale impostato, e per ridimensionare le immagini al loro caricamento (Intervention, la libreria che usiamo per i resize, ne ha bisogno).

Quindi è necessario impostare php.ini con queste due righe:

> extension = intl.so
> extension = fileinfo.so
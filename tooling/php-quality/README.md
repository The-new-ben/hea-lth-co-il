# Hea-lth WordPress PHP quality tools

This toolchain checks only first-party Hea-lth PHP. Vendored packages are excluded and are governed through their pinned release/checksum and upstream security process.

```powershell
& C:\Users\pro\bin\composer.cmd install
vendor\bin\phpcs -p --standard=phpcs.xml.dist
vendor\bin\phpstan analyse -c phpstan.neon.dist
& C:\Users\pro\bin\composer.cmd audit
```

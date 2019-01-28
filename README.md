# My PHP Coding Standard

### Installazione
Lo standard definito è lo standard che in teoria uno vuole utilizzare in tutti i progetti, quindi dev'essere installato globalmente. Per essere utilizzato in modo "agile" va installato l'opportuno plugin dell'editor e configurato.

Usando [SublimeText 3](https://www.sublimetext.com/3), il plugin che consiglio è [Phpcs](https://packagecontrol.io/packages/Phpcs).
Questo plugin può usare diversi tools quali lo [sniffer](https://packagist.org/packages/squizlabs/php_codesniffer), il linter, il [mess detector](https://packagist.org/packages/phpmd/phpmd), il [fixer](https://packagist.org/packages/friendsofphp/php-cs-fixer) etc... che verranno installati globalmente una volta installato questo:
`composer global require elplaza/mypsr`

p.s. per aggiornare i pacchetti installati globalmente lanciare: `composer global update`

Una volta installati i tools, questi, come lo standard da usare, vanno comunicati al plugin tramite il file di configurazione (Preferences --> Package Settings --> PHP Code Sniffer --> Settings User), per es.:
```
{
	"show_debug"                  : true,
	"phpcs_executable_path"       : "~/.composer/vendor/bin/phpcs",
	"phpcbf_executable_path"      : "~/.composer/vendor/bin/phpcbf",
	"php_cs_fixer_executable_path": "~/.composer/vendor/bin/php-cs-fixer",
	"phpmd_executable_path"       : "~/.composer/vendor/bin/phpmd",
	"phpcs_show_quick_panel"      : true,
	"phpcbf_on_save"              : true,
	"php_cs_fixer_on_save"        : false,
	"phpmd_run"                   : true,
	"phpcs_additional_args"       : {
        "--standard": "~/.composer/vendor/elplaza/mypsr/src/MyPSR",
        "-n": ""
    },
    "phpcbf_additional_args": {
        "--standard": "~/.composer/vendor/elplaza/mypsr/src/MyPSR",
        "-n": ""
    }
}
```

### Lanciare i test
Per lanciare i test del MyPSR basta:
- lanciare un test singolo: `./bin/phpunit src/MyPSR/Tests/WhiteSpace/BracketsUnitTest.php`
- lanciare tutti i test: `./bin/phpunit --testsuite MyPSR`

Utile per testare il nostro PSR:
- tutto lo standard: `./bin/phpcs -s -vvv --standard=./src/MyPSR testfile.php`
- solo uno sniff: `./bin/phpcs -s -vvv --standard=./src/MyPSR --sniffs=MyPSR.Arrays.Multiline testfile.php`
- per il fixer: `./bin/phpcbf -vvv --standard=./src/MyPSR --sniffs=MyPSR.Arrays.Multiline testfile.php`

### Comandi utili
Comandi utili:
- stampa la lista dei coding standard installati: `./bin/phpcs -i`
- stampa tutti gli sniff presenti nello standard specificato: `./bin/phpcs -e --standard=PSR2`
- stampa la documentazione per ogni sniff dello standard: `./bin/phpcs --generator=Text --standard=./src/MyPSR`
- stampa la documentazione per il singolo sniff: `./bin/phpcs --generator=Text --standard=./src/MyPSR --sniffs=MyPSR.Arrays.Multiline`

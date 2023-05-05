./vendor/bin/phpstan analyse --memory-limit=1G --level=9 src
./vendor/bin/phpmd src text codesize,unusedcode,naming,design,cleancode      
./vendor/bin/phpcs -ns --report=summary --extensions=php --standard=PSR12 src
./vendor/bin/phpcs src --standard=Squiz --sniffs=Squiz.Commenting.FunctionComment,Squiz.Commenting.FunctionCommentThrowTag,Squiz.Commenting.ClassComment,Squiz.Commenting.VariableComment
./vendor/bin/phpcs -p src --standard=PHPCompatibility --runtime-set testVersion 7.4-
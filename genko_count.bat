@echo off

@rem php.exeをインストールしたパスを指定してください。
if exist "C:\Users\mhibara\Documents\xampp\php\php.exe" (
	"C:\Users\mhibara\Documents\xampp\php\php.exe" genko_count.php -i "yume_juya.txt"
	goto Success
) else (
	goto Error
)

:Error

@echo "php.exeがありません！"


:Success


@pause




1. Установить phpBB. Если SJB лежит в подпапке, то форум должен находится в этой же подпапке.
2. Скопировать файл auth_sjb.php из 3rd party/phpBBIntegration/phpBB/includes/auth в /ваш форум/includes/auth.
3. Зайти в админку форума вкладка GENERAL->Client communication->Authentication и выбрать Sjb в "Select an authentication method:". Сохранить.
4. Скопировать папку phpbb_bridge_plugin из 3rd party/phpBBIntegration/SJB в /system/plugins/
5. Зайти в админку SJB, System Configuration->System Settings->Forum. Указать там, что в системе присутствует форум и путь к нему.
6. скопировать папку install из 3rd party/phpBBIntegration/phpBB/ в папку форума и запустить файл install.php
7. Удалить папку install.
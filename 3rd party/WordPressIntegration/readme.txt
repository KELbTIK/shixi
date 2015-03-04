1. Установить WordPress. Если SJB лежит в подпапке, то WordPress должен находится в этой же подпапке.
2. Зайти в админку WordPress'a: Plugins->Add New->Upload и добавить архив из 3rd party/WordPressIntegration/WordPress/sjb_bridge_plugin.zip
3. Активировать плагин.
4. Скопировать папку wordpress_bridge_plugin из 3rd party/WordPressIntegration/SJB в /system/plugins/
5. Зайти в админку SJB, System Configuration->System Settings->Plugins. Указать там, что в системе присутствует блог и путь к нему.
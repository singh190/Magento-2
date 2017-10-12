# Magento-2
Create a directory named : 'Singh'  which is the namespace for all modules.

use command: <br/>
1> enable module: php bin/magento module:enable Namespace_Module. <br/>
check for module status: php bin/magento module:status.<br/>
2> clear cache:  php bin/magento cache:flush. <br/>
3> upgrade setup: php bin/magento setup:upgrade. <br/>
4> compile setup: php bin/magento setup:di:compile. <br/>
5> indexing: php bin/magento indexer:reindex/info/status. <br/>
6> Install sample data: magento sample data:reset. <br/>
7> Clear static content: bin/magento setup:static-content:deploy <br/>
8> Db setup upgrade: bin/magento setup:db-schema:upgrade <br/>
9> Enable developer mode: bin/magento deploy:mode:set developer<br/>
10> Clear static content and enable module: bin/magento --clear-static-content.<br/>
11> Uninstall and delete module: php bin/magento module:uninstall.<br/>
12> php -d memory_limit=2G bin/magento setup:static-content:deploy .<br/>
php bin/magento admin:user:create --admin-user="admin" --admin-firstname="Abhi" --admin-lastname="Singh" --admin-email="email@address" --admin-password="admin@123"
13> To process CSS/SASS file
php magento dev:source-theme:deploy --type less css/styles-l --locale="en_US" --area="frontend" --theme="Magento/blank"
<br/>
Issue:<br/>
A)Install Magento the Integrator way.<br/>
B)Copy a working env.php to app/etc/<br/>
C)Import an working database dump into mysql.<br/>
D)Open the Site in the Browser/run setup upgrade<br/>
Result: <br/>
Exception #0 (BadMethodCallException): Missing required argument $routerList of Magento\Framework\App\RouterList.<br/>
Solution: <br/>
bin/magento module:enable --all<br/>
bin/magento setup:di:compile<br/>

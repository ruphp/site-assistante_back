

для удаленки
установить  настроить бд создать базу

установить redis
https://www.8host.com/blog/ustanovka-i-zashhita-redis-v-centos-7/sudo   только 1 пункт

1.1 установить композер


cd /bin

                  php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
                  php -r "if (hash_file('sha384', 'composer-setup.php') === 'dac665fdc30fdd8ec78b38b9800061b4150413ff2e3b6f88543c636f7cd84f6db9189d43a81e5503cda447da73c7e5b6') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
                  php composer-setup.php --filename=composer 
                  php -r "unlink('composer-setup.php');"

cd /usr/share/nginx/html/devadm.smguide.ru/

скачать проект
с тфс .

rm -rf ./vendor/
rm -rf ./vagrant/
rm -rf ./test/


cсравниваем файлы с репой меням все ? удаляем лишние 
...........................................................................................................


общее 








php /usr/bin/composer update


настроить env

pзапустить миграции
php yii migrate

проверяем сайт должен работать https://devadm.smguide.ru/

настроить крон

создать юзера или 2х из супер админки , настроить доступ к модулям

зоздать уже ими контент




Добавлять в крон ежеменутно - nano /var/spool/cron/root
проверка crontab -l

создать файл /tmp/yii_cron.log
каждую минуту.

* * * * php /usr/share/nginx/html/devadm.smguide.ru/yii cron/prepare-log-configuration 2>&1 >>/tmp/yii_cron.log 2>&1


-  далее учтанавливаем доп мождули сошласно их redme файлу 


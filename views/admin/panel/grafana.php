<?php

use ruwmapps\yii2_uikit3\LinkPager;

/** @var $pages */
/** @var $users */
$users_label= new \app\models\Users;

$this->title = "Список ссылок на графический мониторинг ";
?>
<h3>Технический мониторинг</h3>
<div class="uk-container uk-margin">

    <table class="uk-table uk-table-striped">

        <tbody>
        <tr><td><span>АСУН ПК</span></td><td><span>Eдиная информационно-аналитическая система управления объектами недвижимого имущества Пермского края </span></td><td><a target='_blank' href='https://mission-control.permkrai.ru/d/000000193/asun-pk?orgId=39&refresh=5m'> смотреть статистику </a></td></tr>
        <tr><td><span>АИС Стройкомплекс ПК</span></td><td><span>Автоматизированная информационная система мониторинга и управления строительной отрасли Пермского края </span></td><td><a target='_blank' href='https://mission-control.permkrai.ru/d/000000189/ais-stroikompleks?orgId=197&refresh=5m'> смотреть статистику </a></td></tr>
        <tr><td><span>АИС Мониторинг Пермского края</span></td><td><span>Аналитическая информационная система Мониторинг Пермского края </span></td><td><a target='_blank' href='https://mission-control.permkrai.ru/d/000000189/ais-monitoring-pk?orgId=213&refresh=5m'> смотреть статистику </a></td></tr>
        <tr><td><span>АИС Большие данные ПК</span></td><td><span>АИС Большие данные ПК </span></td><td><a target='_blank' href='https://mission-control.permkrai.ru/d/000000195/bol-shie-dannye?orgId=198'> смотреть статистику </a></td></tr>
        <tr><td><span>АИС Стройкомплекс - АиВ</span></td><td><span>АИС Стройкомплекс - Подсистема мониторинга аварийного и ветхого жилья Пермского края </span></td><td><a target='_blank' href='https://mission-control.permkrai.ru/d/000000189/ais-stroikompleks?orgId=197&refresh=5m'> смотреть статистику </a></td></tr>
        <tr><td><span>ГИС Аналитика ЭДО</span></td><td><span>Государственная информационная система «Аналитика электронного документооборота Пермского края» </span></td><td><a target='_blank' href='https://mission-control.permkrai.ru/d/000000304/gis-aedo?orgId=206&refresh=5s'> смотреть статистику </a></td></tr>
        <tr><td><span>ГИС КБ ПК</span></td><td><span>Государственная информационная система «Комплексное благоустройство Пермского края» </span></td><td><a target='_blank' href='https://mission-control.permkrai.ru/d/000000195/kb?orgId=193&refresh=5m'> смотреть статистику </a></td></tr>
        <tr><td><span>ГИС ОГ</span></td><td><span>Государственная информационная система «Обращения граждан </span></td><td><a target='_blank' href='https://mission-control.permkrai.ru/d/000000194/gis-og?orgId=192&refresh=5m'> смотреть статистику </a></td></tr>
        <tr><td><span>ГИС РИС ПК</span></td><td><span>Государственная информационная система Пермского края «Реестр информационных систем Пермского края» </span></td><td><a target='_blank' href='https://mission-control.permkrai.ru/d/000000194/gis-ris-pk?orgId=236&refresh=5m'> смотреть статистику </a></td></tr>
        <tr><td><span>РИСОГД Пермского края</span></td><td><span>Государственная региональная информационная система обеспечения градостроительной деятельности </span></td><td><a target='_blank' href='https://mission-control.permkrai.ru/d/000000189/risogd?orgId=187&refresh=5m'> смотреть статистику </a></td></tr>
        <tr><td><span>РСАА ПК</span></td><td><span>Региональный сервис аутентификации и авторизации сотрудников органов государственной власти и учреждений Пермского края </span></td><td><a target='_blank' href='https://mission-control.permkrai.ru/d/000000189/rsaa_2-pk?orgId=243&refresh=5m'> смотреть статистику </a></td></tr>
        <tr><td><span>ЕАИС «Социальный регистр населения»</span></td><td><span>Единая автоматизированная информационная система «Социальный регистр населения» </span></td><td><a target='_blank' href='https://mission-control.permkrai.ru/d/000000195/eais-sotsial-nyi-registr-naseleniia?orgId=202&refresh=5m'> смотреть статистику </a></td></tr>
        <tr><td><span>ЕИС УФХД ПК Аналитическая подсистема</span></td><td><span>Единая информационная система управления финансово-хозяйственной деятельностью организаций государственного сектора Пермского края. </span></td><td>Нет на мониторинге</td></tr>
        <tr><td><span>Управляем Вместе</span></td><td><span>Единая краевая автоматизированная система открытого правительства Пермского края. Управляем вместе. (Кабинеты ОИВ) </span></td><td><a target='_blank' href='https://mission-control.permkrai.ru/d/000000190/upravliaem-vmeste?orgId=38&refresh=5m'> смотреть статистику </a></td></tr>
        <tr><td><span>ЕПС ПК</span></td><td><span>Единая платформа сайтов органов государственной власти Пермского края </span></td><td><a target='_blank' href='https://mission-control.permkrai.ru/d/000000189/eps-pk?orgId=239&refresh=5m'> смотреть статистику </a></td></tr>
        <tr><td><span>ЕСОП ПК</span></td><td><span>Единая система оплаты проезда Пермского края </span></td><td><a target='_blank' href='https://mission-control.permkrai.ru/d/000000304/esop-pk?orgId=227&refresh=5m'> смотреть статистику </a></td></tr>
        <tr><td><span>ЕХД</span></td><td><span>Единое хранилище данных Пермского края </span></td><td><a target='_blank' href='https://mission-control.permkrai.ru/d/000000378/ekhd-pk?orgId=146&refresh=5m'> смотреть статистику </a></td></tr>
        <tr><td><span>ИС МСД</span></td><td><span>Информационная система «Мониторинг судебных дел» </span></td><td><a target='_blank' href='https://mission-control.permkrai.ru/d/0000001893434/is-msd?orgId=248&refresh=5m'> смотреть статистику </a></td></tr>
        <tr><td><span>ИС Спортивное Прикамье</span></td><td><span>Информационная система «Спортивное Прикамье» </span></td><td><a target='_blank' href='https://mission-control.permkrai.ru/d/000000189/is-sportivnoe-prikam-e?orgId=231&refresh=5m'> смотреть статистику </a></td></tr>
        <tr><td><span>ИАС АССИСТЕНТ-КСП</span></td><td><span>Информационно-аналитическая система автоматизации внешнего государственного финансового контроля в Контрольно-счетной палате Пермского края «АССИСТЕНТ-КСП» </span></td><td><a target='_blank' href='https://mission-control.permkrai.ru/d/000000195444/ias-kps?orgId=250&refresh=5m'> смотреть статистику </a></td></tr>
        <tr><td><span>Подсистема мониторинга Комплекса ИС эксплуатации</span></td><td><span>Комплекс информационных систем эксплуатации, подсистема мониторинга </span></td><td><a target='_blank' href='https://mission-control.permkrai.ru/d/000000231/kise?orgId=76'> смотреть статистику </a></td></tr>
        <tr><td><span>АИП ПК</span></td><td><span>Подсистема автоматизации формирования адресной инвестиционной программы Пермского края РИС МКР ПК </span></td><td><a target='_blank' href='https://mission-control.permkrai.ru/d/000000189/aip-pk?orgId=190&refresh=5m'> смотреть статистику </a></td></tr>
        <tr><td><span>АРМ ГМУ</span></td><td><span>Подсистема «Автоматизированное рабочее место Государственных и муниципальных услуг» Единой централизованной сервисной платформы Государственных и муниципальных услуг Пермского края </span></td><td><a target='_blank' href='https://mission-control.permkrai.ru/d/000000195/etssp-arm-gmu?orgId=196&refresh=5m'> смотреть статистику </a></td></tr>
        <tr><td><span>ГИС ИОПР ПК </span></td><td><span>Государственная информационная система «Информационное обеспечение и аналитика объектов потребительского рынка Пермского края» (ГИС ИОПР ПК) </span></td><td><a target='_blank' href='https://mission-control.permkrai.ru/d/000000195/iopr?orgId=222&refresh=5m'> смотреть статистику </a></td></tr>
        <tr><td><span>ПМВД</span></td><td><span>Подсистема модерации выгрузки данных на портал ССТУ </span></td><td>Нет на мониторинге</td></tr>
        <tr><td><span>Подсистема РСО ПК</span></td><td><span>Подсистема подключения (технологического присоединения) к сетям РСО Пермского края </span></td><td>Нет на мониторинге</td></tr>
        <tr><td><span>СтройКонтроль</span></td><td><span>Подсистема «СтройКонтроль» автоматизированной информационной системы мониторинга и управления строительной отрасли Пермского края «АИС Стройкомплекс ПК» </span></td><td><a target='_blank' href='https://mission-control.permkrai.ru/d/000000189/stroikontrol?orgId=216&refresh=5m'> смотреть статистику </a></td></tr>
        <tr><td><span>План IT</span></td><td><span>Подсистема формирования и обеспечения реализации плана мероприятий в сфере информационно-коммуникационных технологий </span></td><td><a target='_blank' href='https://mission-control.permkrai.ru/d/000000229/plan-it?orgId=209&refresh=5m'> смотреть статистику </a></td></tr>
        <tr><td><span>РГИС Пермского края</span></td><td><span>Региональная геоинформационная система Пермского края </span></td><td><a target='_blank' href='https://mission-control.permkrai.ru/d/000000195/rgis?orgId=214&refresh=5m'> смотреть статистику </a></td></tr>
        <tr><td><span>РГИС «Умный лес»</span></td><td><span>Региональная государственная информационная система (РГИС «Умный лес») </span></td><td><a target='_blank' href='https://mission-control.permkrai.ru/d/000000194/umnyi-les?orgId=218&refresh=5s'> смотреть статистику </a></td></tr>
        <tr><td><span>РИС ЗАКУПКИ ПК</span></td><td><span>Региональная информационная система в сфере закупок товаров, работ, услуг </span></td><td><a target='_blank' href='https://mission-control.permkrai.ru/d/000000189/ris-zakupki-pk?orgId=240&refresh=5m'> смотреть статистику </a></td></tr>
        <tr><td><span>РИС МКР</span></td><td><span>Региональная информационная система по мониторингу комплексного развития </span></td><td><a target='_blank' href='https://mission-control.permkrai.ru/d/000000195/ris-mkr-pk?orgId=41&refresh=5m'> смотреть статистику </a></td></tr>
        <tr><td><span>УЗПК</span></td><td><span>РИСОГД Подсистема управления землями Пермского края </span></td><td>Нет на мониторинге</td></tr>
        <tr><td><span>Система логирования</span></td><td><span>Система логирования Комплекса ИС эксплуатации </span></td><td><a target='_blank' href='https://mission-control.permkrai.ru/d/000000231/kise?orgId=76'> смотреть статистику </a></td></tr>
        <tr><td><span>Система статического анализа кода</span></td><td><span>Система статического анализа кода </span></td><td><a target='_blank' href='https://mission-control.permkrai.ru/d/000000231/kise?orgId=76'> смотреть статистику </a></td></tr>
        <tr><td><span>СУИ</span></td><td><span>Система управления инцидентами </span></td><td><a target='_blank' href='https://mission-control.permkrai.ru/d/000000189/sdo?orgId=37&refresh=5m'> смотреть статистику </a></td></tr>
        <tr><td><span>СДО ПК</span></td><td><span>Система Дистанционного Обучения Пермского края </span></td><td><a target='_blank' href='https://mission-control.permkrai.ru/d/000000189/sdo?orgId=37&refresh=5m'> смотреть статистику </a></td></tr>
        <tr><td><span>ЕГАИС АП</span></td><td><span>Архивы Прикамья </span></td><td><a target='_blank' href='https://mission-control.permkrai.ru/d/00000018911/egais-arkhivy-prikam-ia?orgId=246&refresh=5m'> смотреть статистику </a></td></tr>
        <tr><td><span>ЦИПП ПК</span></td><td><span>Центр интерактивной поддержки пользователей Пермского края </span></td><td><a target='_blank' href='https://mission-control.permkrai.ru/d/000000195/tsipp-pk?orgId=226&refresh=10s'> смотреть статистику </a></td></tr>


        </tbody>
        <!-- -->
    </table>
  
</div>
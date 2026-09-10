<?php
$arUrlRewrite=array (
  6 =>
  array (
    'CONDITION' => '#^/courses/([^/]+)/lessons/([^/]+)/?$#',
    'RULE' => 'course=$1&lesson=$2',
    'ID' => 'bitrix-app:lesson.detail',
    'PATH' => '/courses/lesson.php',
    'SORT' => 90,
  ),
  5 =>
  array (
    'CONDITION' => '#^/courses/([^/]+)/?$#',
    'RULE' => 'code=$1',
    'ID' => 'bitrix-app:course.detail',
    'PATH' => '/courses/detail.php',
    'SORT' => 100,
  ),
  1 => 
  array (
    'CONDITION' => '#^\\/?\\/mobileapp/jn\\/(.*)\\/.*#',
    'RULE' => 'componentName=$1',
    'ID' => NULL,
    'PATH' => '/bitrix/services/mobileapp/jn.php',
    'SORT' => 100,
  ),
  3 => 
  array (
    'CONDITION' => '#^/bitrix/services/ymarket/#',
    'RULE' => '',
    'ID' => '',
    'PATH' => '/bitrix/services/ymarket/index.php',
    'SORT' => 100,
  ),
  0 => 
  array (
    'CONDITION' => '#^/stssync/calendar/#',
    'RULE' => '',
    'ID' => 'bitrix:stssync.server',
    'PATH' => '/bitrix/services/stssync/calendar/index.php',
    'SORT' => 100,
  ),
  2 => 
  array (
    'CONDITION' => '#^/rest/#',
    'RULE' => '',
    'ID' => NULL,
    'PATH' => '/bitrix/services/rest/index.php',
    'SORT' => 100,
  ),
  4 => 
  array (
    'CONDITION' => '#^/news/#',
    'RULE' => '',
    'ID' => 'bitrix:news',
    'PATH' => '/news/index.php',
    'SORT' => 100,
  ),
);

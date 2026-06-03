<?php
/**
 * @var array $list
 * @var string $pathInfo
 */



$this->beginBlock('block_left_menu');
if (count($list) || count($lists)) {
    ?>
    <div class="uk-width-1-6@s">
        <ul class="uk-nav uk-nav-default uk-nav-parent-icon" uk-nav>
            <?php
            $li = '';

            foreach ($list as $k => $v) {
                $active = $pathInfo == $k ? 'class="uk-active"' : '';
                   // debug($v.'-----',1);
                    $li .= '<li ' . $active . '><a href="/' . $k . '">' . $v . '</a></li>';

            }

            ////////////////////////
            foreach ($lists as $key => $list) {
               // debug($k);
                if($key =='#'){


                    foreach ($list as $k2 => $v2) {
                        //debug($k2);
                        $active = str_contains($pathInfo, $k2) ? 'class="uk-parent uk-open "' : 'class="uk-parent"';
                        $aria_hidden = str_contains($pathInfo, $k2) !== false ? ' class="uk-nav-sub"   aria-hidden="false" ' : ' class="uk-nav-sub"   aria-hidden="true" ';
                        if (str_contains($pathInfo, $k2) !== false) {

                            break;
                        }
                    }
                    $li .= '<li ' . $active . '><a href="#">' .$list[0] . '<span uk-nav-parent-icon></span></a> <ul ' . $aria_hidden . '>';// debug($li,1);
                    $list = array_slice($list, 1);

                    foreach ($list as $k2 => $v2) {
                        $active = $pathInfo == $k2 ? 'class="uk-active"' : '';
                        $li .= '<li ' . $active . '><a href="/' . $k2 . '">' . $v2 . '</a></li>';
                    }


                    $li .= '</ul></li>';
                }
                else if(isset($list['#'])){


                    foreach ($list['#'] as $k2 => $v2) {
                        //debug($k2);
                        $active = str_contains($pathInfo, $k2) ? 'class="uk-parent uk-open "' : 'class="uk-parent"';
                        $aria_hidden = str_contains($pathInfo, $k2) !== false ? ' class="uk-nav-sub"   aria-hidden="false" ' : ' class="uk-nav-sub"   aria-hidden="true" ';
                        if (str_contains($pathInfo, $k2) !== false) {

                            break;
                        }
                    }
                    $li .= '<li ' . $active . '><a href="#">' .$list['#'][0] . '<span uk-nav-parent-icon></span></a> <ul ' . $aria_hidden . '>';// debug($li,1);
                    $list = array_slice($list['#'], 1);

                    foreach ($list as $k2 => $v2) {
                        $active = $pathInfo == $k2 ? 'class="uk-active"' : '';
                        $li .= '<li ' . $active . '><a href="/' . $k2 . '">' . $v2 . '</a></li>';
                    }


                    $li .= '</ul></li>';
                }
                else{
                    foreach ($list as $k => $v) {
                        $active = $pathInfo == $k ? 'class="uk-active"' : '';
                        $li .= '<li ' . $active . '><a href="/' . $k . '">' . $v . '</a></li>';
                    }

                }

            }



            echo $li;
            ?>


        </ul>
    </div>
    <?php
}
$this->endBlock();
?>


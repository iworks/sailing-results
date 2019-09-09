#!/usr/bin/php -d memory_limit=1024M
<?php

if ( !isset( $argv[1] ) || empty( $argv[1] ) ) {
    echo 'enter filename';
    echo PHP_EOL;
    exit;
}
$filename = $argv[1];
if ( ! is_file( $filename ) || ! is_readable( $filename ) ) {
    printf( 'filename "%s" does not exists or is not readable', $filename );
    echo PHP_EOL;
    exit;
}


$data = explode( PHP_EOL, file_get_contents( $filename ) );

$show_sailors = true;
if ( isset( $argv[2] ) && 'country' === $argv[2] ) {
    $show_sailors = false;
}
$show_countries = !$show_sailors;

$default_country = 'unknown';
if ( isset( $argv[3] ) ) {
    $default_country = $argv[3];
}

$show_country = true;
if ( isset( $argv[4] ) && 'hide' === $argv[4] ) {
    $show_country = false;
}

$is_place_added = false;

foreach( $argv as $a ) {
    if ( 'hide-country' === $a ) {
        $show_country = false;
    }
    if ( 'place-is-added' === $a ) {
        $is_place_added = true;
    }
}


$sailors = array();
$countries = array();

foreach( $data as $row ) {
    $x = explode( ',', $row );
    $year = intval( array_shift( $x ) );
    $place = 1;
    if ( $is_place_added ) {
        array_shift( $x );
    }
    foreach( $x as $v ) {
        $v = trim( $v );
        if ( empty( $v ) ) {
            continue;
        }
        $country = $default_country;
        if ( preg_match( '/^([A-Z]{3}) /', $v, $matches ) ) {
            $country = $matches[1];
            $v = preg_replace( '/^.{4}/', '', $v );
            $v = trim( $v );
        }
        $s = explode( '/', $v );

        if ( $show_country ) {
            if ( preg_match( '/^(BRD|GDR|DDR|FRG|GER)$/i', $country ) ) {
                $country = 'DEU';
            }
            if ( preg_match( '/^(SFR)$/i', $country ) ) {
                $country = 'YUG|SFR';
            }
            if ( preg_match( '/^(PRL)$/i', $country ) ) {
                $country = 'POL';
            }
            if ($default_country === $country ) {
                print_r( $row );die;
            }
        }

        foreach ( $s as $one ) {
            $one = trim( $one );
            if ( empty( $one ) ) {
                continue;
            }
            if ( preg_match( '/^([A-Z]{3})[ \t]+(.+)$/', $one, $matches ) ) {
                $country = $matches[1];
                $one = trim( $matches[2] );
                if ( empty( $one ) ) {
                    continue;
                }
            }
            if ( ! isset( $sailors[ $one ] ) ) {
                $sailors[ $one ] = array(
                    '1' => 0,
                    '2' => 0,
                    '3' => 0,
                    'country' => $country,
                    'start' => PHP_INT_MAX,
                    'end' => 0,
                );
            }
            $sailors[$one]['name'] = $one;
            if ( $year < $sailors[ $one ]['start' ] ) {
                $sailors[$one]['start'] = $year;
            }
            if ( $year > $sailors[ $one ]['end' ] ) {
                $sailors[$one]['end'] = $year;
            }
            if ( ! isset( $countries[ $country ] ) ) {
                $countries[ $country ] = array(
                    '1' => 0,
                    '2' => 0,
                    '3' => 0,
                    'start' => 0,
                    'name' => $country,
                );
            }
            $sailors[ $one ][ $place ]++;
        }
        $countries[ $country] [ $place]++;
        $place++;
    }
}


$names = get_names();

if ( $show_sailors ) {

    // echo '{| class="wikitable sortable"';
    // echo PHP_EOL;
    // echo '! Miejsce';
    // echo PHP_EOL;
    // echo '! Zawodnik';
    // echo PHP_EOL;
    // if ( $show_country ) {
        // echo '! Państwo';
        // echo PHP_EOL;
    // }
    // echo '! Lata<ref group=uwaga>Lata, w których dany zawodnik zdobywał medale mistrzostw polski.</ref>';
    // echo PHP_EOL;
    // echo '! Złoto';
    // echo PHP_EOL;
    // echo '! Srebro';
    // echo PHP_EOL;
    // echo '! Brąz';
    // echo PHP_EOL;
    // echo '! Razem';
    // echo PHP_EOL;


    uasort( $sailors, 'sort_by_medals'  );
    $i = 1;
    $comb = $i;
    $last = get_sum ($sailors[ array_keys( $sailors )[0] ] );
    foreach( $sailors as $name => $data ) {
        $current = get_sum( $data );
        echo '|- align = center';
        echo PHP_EOL;
        echo '| ';
        if ( $last === $current ) {
            echo $comb;
        } else {
            echo $i;
        }
        echo '.';
        echo PHP_EOL;
        /**
         * Name
         */
        echo '| align = left | ';
        if ( array_key_exists( $name, $names ) ) {
            if ( $name != $names[ $name ] ) {
                printf( '{{sortname|%s|%s}}', implode( '|', explode( ' ', $name ) ), $names[ $name ] );
            } else {
                printf( '{{sortname|%s}}', implode( '|', explode( ' ', $name ) ) );
            }
        } else {
            echo $name;
        }
        echo PHP_EOL;
        if ( $show_country ) {
            printf( '| align = left | {{Państwo|%s}}', $data['country'] );
            echo PHP_EOL;
        }
        echo '| ';
        echo $data['start'];
        if ( $data['start'] !== $data['end'] ) {
            echo ' – ';
            echo $data['end'];
        }
        echo PHP_EOL;
        echo '| {{Żużel/Kolor|m1}} | '.$data['1'];
        echo PHP_EOL;
        echo '| {{Żużel/Kolor|m2}} | '.$data['2'];
        echo PHP_EOL;
        echo '| {{Żużel/Kolor|m3}} | '.$data['3'];
        echo PHP_EOL;
        echo '| ';
        echo $data['1'] + $data['2'] + $data['3'];
        echo PHP_EOL;
        if ( $last !== $current ) {
            $comb = $i;
        }
        $i++;
        $last = $current;
    }
    // echo '|}';
}

if ( $show_countries ) {
    uasort( $countries, 'sort_by_medals' );
    $i = 1;
    $comb = $i;
    $last = get_sum ($countries[ array_keys( $countries )[0] ] );
    foreach( $countries as $name => $data ) {
        $current = get_sum( $data );
        echo '|- align = center';
        echo PHP_EOL;
        echo '| ';
        if ( $last === $current ) {
            echo $comb;
        } else {
            echo $i;
        }
        echo '.';
        echo PHP_EOL;
        printf( '| align = left | {{Państwo|%s}}', $name );
        echo PHP_EOL;
        echo '| {{Żużel/Kolor|m1}} | '.$data['1'];
        echo PHP_EOL;
        echo '| {{Żużel/Kolor|m2}} | '.$data['2'];
        echo PHP_EOL;
        echo '| {{Żużel/Kolor|m3}} | '.$data['3'];
        echo PHP_EOL;
        echo '| ';
        echo $data['1'] + $data['2'] + $data['3'];
        echo PHP_EOL;
        $i++;
        if ( $last !== $current ) {
            $comb = $i;
        }
        $last = $current;
    }
}




function get_sum( $data ) {
    return md5( sprintf( '%d-%d-%d', $data['1'], $data['2'], $data['3'] ) );
}


function sort_by_medals( $a, $b ) {
    if ( $a['1'] > $b['1'] ) {
        return -1;
    } else if ( $a['1'] === $b['1'] ) {
        if ( $a['2'] > $b['2'] ) {
            return -1;
        } else if ( $a['2'] === $b['2'] ) {
            if ( $a['3'] > $b['3'] ) {
                return -1;
            } else if ( $a['3'] === $b['3'] ) {
                if ( $a['start'] > $b['start'] ) {
                    return 1;
                }
                else if ( $a['start'] === $b['start'] ) {
                    return strcmp( $a['name'], $b['name'] );
                }
                return 0;
            }
        }
    }
    return 1;
}

function get_names() {
    return array(
        'Agnieszka Skrzypulec'      => 'Agnieszka Skrzypulec',
        'Alejandro Abascal'         => 'Alejandro Abascal',
        'Alejandro Abascal García'  => 'Alejandro Abascal García',
        'Alex Maloney'              => 'Alex Maloney',
        'André Nelis'               => 'André Nelis',
        'Andrew Simpson'            => 'Andrew Simpson',
        'Andrzej Iwiński'           => 'Andrzej Iwiński (żeglarz)',
        'Andrzej Rymkiewicz'        => 'Andrzej Rymkiewicz',
        'Andrzej Zawieja'           => 'Andrzej Zawieja',
        'Ben Ainslie'               => 'Ben Ainslie',
        'Bogdan Kramer'             => 'Bogdan Kramer',
        'Chris Cook'                => 'Chris Cook',
        'Czesław Marchaj'           => 'Czesław Marchaj',
        'Dominik Życki'             => 'Dominik Życki',
        'Eckart Diesch'             => 'Eckart Diesch',
        'Fredrik Lööf'              => 'Fredrik Lööf',
        'Henryk Blaszka'            => 'Henryk Blaszka',
        'Hubert Raudaschl'          => 'Hubert Raudaschl',
        'Jacques Lebrun'            => 'Jacques Lebrun',
        'Jacques Baptiste Lebrun'   => 'Jacques Lebrun',
        'Jens Bojsen-Møller'        => 'Jens Bojsen-Møller',
        'Jonas Høgh-Christensen'    => 'Jonas Høgh-Christensen',
        'Jonathan Lobert'           => 'Jonathan Lobert',
        'Jonathan McKee'            => 'Jonathan McKee',
        'Jørgen Bojsen-Møller'      => 'Jørgen Bojsen-Møller',
        'Juliusz Sieradzki'         => 'Juliusz Sieradzki',
        'Karol Jabłoński'           => 'Karol Jabłoński',
        'Lech Poklewski'            => 'Lech Poklewski',
        'Lisa Westerhof'            => 'Lisa Westerhof',
        'Lucas Calabrese'           => 'Lucas Calabrese',
        'Ludwik Raczyński'          => 'Ludwik Raczyński',
        'Łukasz Zakrzewski'         => 'Łukasz Zakrzewski',
        'Mateusz Kusznierewicz'     => 'Mateusz Kusznierewicz',
        'Max Salminen'              => 'Max Salminen',
        'Michał Burczyński'         => 'Michał Burczyński',
        'Miguel Noguer'             => 'Miguel Noguer',
        'Paul Elvstrøm'             => 'Paul Elvstrøm',
        'Paul Foerster'             => 'Paul Foerster',
        'Peter Barrett'             => 'Hubert Raudaschl',
        'Peter Lang'                => 'Peter Lang',
        'Piotr Burczyński'          => 'Piotr Burczyński',
        'Piotr Kula'                => 'Piotr Kula',
        'Rafael Trujillo Villar'    => 'Rafael Trujillo Villar',
        'Rafael Trujillo (żeglarz)' => 'Rafael Trujillo Villar',
        'Rafał Szukiel'             => 'Rafał Szukiel',
        'Reinaldo Conrad'           => 'Reinaldo Conrad',
        'Rolly Tasker'              => 'Rolly Tasker',
        'Ron Sherry'                => 'Ron Sherry',
        'Ryszard Blaszka'           => 'Ryszard Blaszka',
        'Ryszard Skarbiński'        => 'Ryszard Skarbiński',
        'Serge Maury'               => 'Serge Maury',
        'Sime Fantela'              => 'Sime Fantela',
        'Terence Neilson'           => 'Terence Neilson',
        'Terry Neilson'             => 'Terence Neilson',
        'Tomasz Rumszewicz'         => 'Tomasz Rumszewicz',
        'Tomasz Zakrzewski'         => 'Tomasz Zakrzewski',
        'Vasilij Žbogar'            => 'Vasilij Žbogar',
        'Wilhelm Kuhweide'          => 'Wilhelm Kuhweide',
        'Władysław Stefanowicz'     => 'Władysław Stefanowicz',
        'Włodzimierz Radwaniecki'   => 'Włodzimierz Radwaniecki',
        'Zach Railey'               => 'Zach Railey',

    );
}

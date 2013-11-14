#!/usr/bin/php
<?php
$dev = (isset($argv[2]) && $argv[2] == 'prod') ? 0 : 1;
require_once 'solarlite-bootstrap.php';
require_once '../lib/imdb.class.php';

$r = new IMDB($argv[1]);

if (!$r->isReady) {
    die($argv[1] . ' Not found' . PHP_EOL);
}

$series = array(
    'title' => $r->getTitle(),
    'description' => $r->getPlot(),
    'release_date' => trim(str_replace('(USA)', '', $r->getReleaseDate())),
    'language_id' => $model->languages->fetchLanguageByName(explode(' / ', $r->getLanguages())[0]),
    'genres' => str_replace(' / ', ',', $r->getGenre()),
    'taglines' => $r->getTagline(),
    'year' => $r->getYear(),
    'seasons' => explode(' / ',$r->getSeasons()),
);

function cast($str)
{
    $arr = explode(' / ', $str);
    $arr2 = array();
    foreach ($arr as $r) {
        if (stristr($r, '&nbsp;')) {
            $parts = explode(' as &nbsp;', $r);
        } else {
            $parts = explode(' as ', $r);
        }
        $arr2[] = array(
            'actor' => isset($parts[0]) ? trim($parts[0]) : null, 
            'character' => isset($parts[1]) ? trim($parts[1]) : null,
        );
    }
    return $arr2;
}


$id = regexMatch('tt[0-9]+', $r->getUrl());

// new series
$series_id = $model->series->createSeries($series);
$tags = App_Util::tagsArray($series['genres']);
$model->series_genres->updateSeriesGenres($series_id, $tags);
$model->series_taglines->updateTaglines($series_id, array($series['taglines']));

echo "Created Series: " . $series['title'] . PHP_EOL;

$seasons = array();
if ($id) {
    foreach ($series['seasons'] as $season) {
        $url = 'http://www.imdb.com/title/'.$id.'/episodes?season=' . $season;
        $html = file_get_contents($url);
    
        // Create new DOM object:
        $dom = new DomDocument();
    
        // Load HTML code:
        @$dom->loadHTML($html);
    
        $xpath = new DOMXPath($dom);
        $details = $xpath->query('//div[@class="info"]');
        $episodes = array();

        for ($i = 0; $i < $details->length; $i++) {
            $episode = array();
            foreach ($details->item($i)->childNodes as $k => $node) {
                if ($k == 2) {
                    $episode['release_date'] = trim($node->textContent);
                }
                
                if ($k == 4) {
                    $episode['link'] = @$node->childNodes->item(0)->attributes->getNamedItem('href')->nodeValue;
                    $episode['title'] = trim($node->textContent);
                }
                
                if ($k == 6) {
                    $episode['description'] = trim($node->textContent);
                }
            }
            $episodes[] = $episode;
        }
        $seasons[$season]['episodes'] = $episodes;
        usleep(500000);
    }
}

foreach ($seasons as $num => $s) {
    if (!empty($s['episodes'])) {
        $season_id = $model->seasons->createSeason($series_id, array('number' => $num, 'year' => regexMatch('(19|20)\d{2}', $s['episodes'][0]['release_date'])));
        echo "Created Season: " . $num . PHP_EOL;
        foreach ($s['episodes'] as $k => $e) {
            $e['number'] = $k + 1;
            $episode_id = $model->episodes->createEpisode($series_id, $season_id, $e);
            echo "Created Episode: " . $e['title'] . PHP_EOL;
            if ($e['link']) {
                $url = 'http://www.imdb.com'.$e['link'];
                $ep = new IMDB($url);
                $e['cast'] = cast($ep->getCastAndCharacter());
                if ($e['cast']) {
                    foreach ($e['cast'] as $c) {
                        if (!$c['actor']) {
                            continue;
                        }
                        $actor_id = $model->actors->fetchByName($c['actor']);
                        if (!$actor_id) {
                            $actor_id = $model->actors->createActorBlank($c['actor']);
                        }
                        $model->episodes_actors->createRelationship($episode_id, $actor_id, $c['character']);
                        echo "Created Episode Actor/Actress: " . $c['actor'] . PHP_EOL;
                    }
                }
                usleep(500000);
            }
        }
    }
}

echo "Done!" . PHP_EOL;

function regexMatch($regex, $content)
{
    preg_match('/'.$regex.'/',$content, $matches);
    return isset($matches[0]) ? $matches[0] : null;
}


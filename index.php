<?php
echo "Никнейм: ";
$_GET['nickname'] = trim(fgets(STDIN));

echo "Клан: ";
$_GET['clan'] = trim(fgets(STDIN));

echo "Ранг: ";
$_GET['rank'] = trim(fgets(STDIN));

echo "URL жетона: ";
$_GET['badge'] = trim(fgets(STDIN));

echo "URL значка: ";
$_GET['mark'] = trim(fgets(STDIN));

echo "URL нашивки: ";
$_GET['stripe'] = trim(fgets(STDIN));


echo "Количество часов: ";
$_GET['time'] = trim(fgets(STDIN));

echo "Количество PvP: ";
$_GET['pvp'] = trim(fgets(STDIN));

echo "Количество PvE: ";
$_GET['pve'] = trim(fgets(STDIN));

echo "У/С: ";
$_GET['stat'] = trim(fgets(STDIN));

echo "Любимый класс PvP: ";
$_GET['pvp_class'] = trim(fgets(STDIN));

echo "Любимый класс PvE: ";
$_GET['pve_class'] = trim(fgets(STDIN));


class UserbarGenerator
{
    private $image;
    private $fontPath = 'regular.ttf';
    private $fontPath2 = 'static.ttf';

    public function __construct()
    {
        if (!extension_loaded('imagick')) {
            throw new \Exception('Расширение Imagick не установлено. Установите его для работы скрипта.');
        }

        $this->image = new \Imagick();
        $this->image->newImage(911, 133, new \ImagickPixel('transparent'));
        $this->image->setImageFormat('png');
    }

    public function generate($nickname = 'Клеймор', $clan = ' ', $rank = 100, $stats = ['time' => '0 ч.', 'class1' => 'ШТУРМОВИК', 'stat1' => '0', 'class2' => 'ШТУРМОВИК', 'stat2' => '0', 'kd' => '0.0'], $badgeUrl = '', $markUrl = '', $stripeUrl = '')
    {
        $this->addBackground();
        $this->addStripe($stripeUrl);
        $this->addBadgeAndMark($badgeUrl,$markUrl);
        $this->addRank($rank);
        $this->addNickname($nickname);
        $this->addClan($clan);
        $this->addLog();
        $this->addStat($stats);
        $this->saveImage();
    }
    private function addBackground()
    {
        $background = new \Imagick('background.png');
        $background->scaleImage(900, 133.3); // Масштабируем фоновое изображение
        $this->image->compositeImage($background, \Imagick::COMPOSITE_OVER, 0, 0);
    }
    private function addImageFromUrl($url, $x, $y)
    {
        if ($url) {
            $image = new \Imagick();
            $image->readImageBlob(file_get_contents($url));
            $this->image->compositeImage($image, \Imagick::COMPOSITE_OVER, $x, $y);
        }
    }
    private function addStripe($stripeUrl = '')
    {
        if (!$stripeUrl == '' && !$stripeUrl == '0'){
	$pageDocument = @file_get_contents($stripeUrl);
	
	if ($pageDocument === false) {
    		die("Указанная нашивка не найдена");
	}
            $image = new \Imagick();
            $image->readImageBlob(file_get_contents($stripeUrl));
        // Позиционируем нашивку слева от жетона и значка, под ними
	$image->scaleImage(512, 128);
        $this->image->compositeImage($image, \Imagick::COMPOSITE_OVER, 60, 3);
        }
    }

    private function addBadgeAndMark($badgeUrl = '', $markUrl = '')
    {
        if (!$badgeUrl == '' && !$badgeUrl == '0') {
        $pageDocument = @file_get_contents($badgeUrl);

        if ($pageDocument === false) {
                die("Указанный жетон не найден");
        }
            $badge = new \Imagick();
            $badge->readImageBlob(file_get_contents($badgeUrl));
	    $badge->scaleImage(128,128);
            $badgeWidth = $badge->getImageWidth();
            // Позиционирование жетона справа от нашивки
            $this->image->compositeImage($badge, \Imagick::COMPOSITE_OVER, 0, 0);
        }

        if (!$markUrl == '' && !$markUrl == '0') {
        $pageDocument = @file_get_contents($markUrl);

        if ($pageDocument === false) {
                die("Указанный значок не найден");
        }
            $mark = new \Imagick();
            $mark->readImageBlob(file_get_contents($markUrl));
	    $mark->scaleImage(128,128);
            // Позиционирование значка по центру жетона
            $markWidth = $mark->getImageWidth();
            $centerX = 0 + (128 - 128) / 2;
            $this->image->compositeImage($mark, \Imagick::COMPOSITE_OVER, $centerX, 0); // Центрируем значок на жетоне
        }
    }
    private function addNickname($nickname)
    {
        $draw = new \ImagickDraw();
        $draw->setFont($this->fontPath);
        $draw->setFontSize(28);
        $draw->setFillColor(new \ImagickPixel('rgba(90%, 90%, 90%, 1.0)'));
        $this->image->annotateImage($draw, 207, 73, 0, $nickname);
    }

    private function addClan($clan)
    {
        $draw = new \ImagickDraw();
        $draw->setFont($this->fontPath);
        $draw->setFontSize(26);
        $draw->setFillColor(new \ImagickPixel('rgba(220, 210, 0, 1.0)'));
        $this->image->annotateImage($draw, 207, 48, 0, $clan);
    }

    private function addStat($stats)
    {
        $draw = new \ImagickDraw();
        $draw->setFont($this->fontPath2);
        $draw->setFontSize(10);
        $draw->setFillColor(new \ImagickPixel('rgba(220, 210, 0, 1.0)'));
        $y = 41;
        foreach ($stats as $stat) {
            $this->image->annotateImage($draw, 637, $y, 0, $stat);
            $y += 14;
        }
    }
    private function addLog()
    {
        $background = new \Imagick('ru_log.png');
        $background->scaleImage(28, 52); // Масштабируем фоновое изображение
        $this->image->compositeImage($background, \Imagick::COMPOSITE_OVER, 600, 32);
    }
    private function addRank($rank = 100)
    {
        $background = new \Imagick('ranks_all.png');
        $background->cropImage(32, 32, 0, ($rank - 1) * 32);
        $background->scaleImage(64, 64); // Масштабируем фоновое изображение
        $this->image->compositeImage($background, \Imagick::COMPOSITE_OVER, 128, 35);
    }    private function saveImage()
    {	$id=time();
        $this->image->resizeImage(432, 64, \Imagick::FILTER_LANCZOS, 1);
        $this->image->writeImage($id.'.png');
        $this->image->destroy();
        echo $id;
    }
}
$generator = new UserbarGenerator();
$generator->generate(
    $_GET['nickname'] ?: 'Ноуклип',
    $_GET['clan']?: '',
    $_GET['rank'] ?: 100,
    [
        'time' => ($_GET['time'] ?: '0') . ' ч.',
        'class1' => ($_GET['pve_class'] ?: 'Н/Д'),
        'stat1' => $_GET['pve'] ?: 0,
        'class2' => $_GET['pvp_class'] ?: 'Н/Д',
        'stat2' => $_GET['pvp'] ?: 0,
        'kd' => $_GET['stat'] ?: 0
    ],
    $_GET['badge'] ?: '',
    $_GET['mark'] ?: '',
    $_GET['stripe'] ?: ''
);

$directory = '.';
$files = scandir($directory);

foreach ($files as $file) {
    if (preg_match('/^\d+\.png$/', $file)) {
        $timestamp = intval(basename($file, '.png'));
        if (time() - $timestamp > 60) {
            $filePath = $directory . '/' . $file;
            if (unlink($filePath)) {
            } else {
            }
        }
    }
}
?>
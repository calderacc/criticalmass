<?php declare(strict_types=1);

namespace App\Criticalmass\Heatmap\Tile;

use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Imagine\Image\ImagineInterface;
use Imagine\Image\Palette\RGB as RGBPalette;

class TileFactory
{
    /** @var ImagineInterface $imagine */
    protected $imagine;

    public function __construct()
    {
        $this->imagine = (new Imagine());
    }

    public function create(int $tileX, int $tileY, int $zoomLevel, ImageInterface $image = null): Tile
    {
        $tile = new Tile($tileX, $tileY, $zoomLevel);

        if (!$image) {
            $box = new Box(Tile::SIZE, Tile::SIZE);
            $transparency = (new RGBPalette())->color('#FFFFFF', 0);
            $image = $this->imagine->create($box, $transparency);
        }

        $tile->setImage($image);

        return $tile;
    }
}

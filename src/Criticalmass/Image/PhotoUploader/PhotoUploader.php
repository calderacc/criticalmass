<?php declare(strict_types=1);

namespace App\Criticalmass\Image\PhotoUploader;

use App\Entity\Photo;
use App\Event\Photo\PhotoUploadedEvent;
use DirectoryIterator;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class PhotoUploader extends AbstractPhotoUploader
{
    public function addFile(string $filename): PhotoUploaderInterface
    {
        $this->createPhotoEntity($filename);

        $this->doctrine->getManager()->flush();

        return $this;
    }

    public function addUploadedFile(UploadedFile $uploadedFile): PhotoUploaderInterface
    {
        $this->createUploadedPhotoEntity($uploadedFile);

        $this->doctrine->getManager()->flush();

        return $this;
    }

    public function addDirectory(string $directoryName): PhotoUploaderInterface
    {
        $dir = new DirectoryIterator($directoryName);

        foreach ($dir as $fileinfo) {
            if (!$fileinfo->isDot()) {
                $this->addFile($fileinfo->getPathname());
            }
        }

        $this->doctrine->getManager()->flush();

        return $this;
    }

    public function getAddedPhotoList(): array
    {
        return $this->addedPhotoList;
    }

    protected function createUploadedPhotoEntity(UploadedFile $uploadedFile): Photo
    {
        $photo = new Photo();

        $photo
            ->setUser($this->user)
            ->setRide($this->ride)
            ->setCity($this->ride->getCity())
            ->setImageFile($uploadedFile);

        $this->doctrine->getManager()->persist($photo);

        $this->eventDispatcher->dispatch(PhotoUploadedEvent::NAME, new PhotoUploadedEvent($photo, true, $uploadedFile->getRealPath()));

        $this->addedPhotoList[] = $photo;

        return $photo;
    }

    protected function createPhotoEntity(string $sourceFilename): Photo
    {
        $photo = new Photo();

        $photo
            ->setUser($this->user)
            ->setRide($this->ride)
            ->setCity($this->ride->getCity());

        $this->uploadFaker->fakeUpload($photo, 'imageFile', file_get_contents($sourceFilename));

        $this->doctrine->getManager()->persist($photo);

        $this->addedPhotoList[] = $photo;

        $this->eventDispatcher->dispatch(PhotoUploadedEvent::NAME, new PhotoUploadedEvent($photo));

        return $photo;
    }
}

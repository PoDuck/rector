<?php

declare(strict_types=1);

namespace Rector\PSR4\Tests\Rector\Namespace_\MultipleClassFileToPsr4ClassesRector;

use Iterator;
use Rector\Core\Application\FileSystem\RemovedAndAddedFilesCollector;
use Rector\FileSystemRector\ValueObject\AddedFileWithContent;
use Rector\PSR4\Rector\Namespace_\MultipleClassFileToPsr4ClassesRector;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;
use Symplify\SmartFileSystem\SmartFileSystem;

final class MultipleClassFileToPsr4ClassesRectorTest extends AbstractRectorTestCase
{
    /**
     * @param AddedFileWithContent[] $expectedFilePathsWithContents
     * @dataProvider provideData()
     */
    public function test(
        SmartFileInfo $originalFileInfo,
        array $expectedFilePathsWithContents,
        bool $shouldDeleteOriginalFile
    ): void {
        /** @var RemovedAndAddedFilesCollector $removedAndAddedFilesCollector */
        $removedAndAddedFilesCollector = self::$container->get(RemovedAndAddedFilesCollector::class);
        $removedAndAddedFilesCollector->reset();

        $this->doTestFileInfo($originalFileInfo);
        $this->assertFilesWereAdded($expectedFilePathsWithContents);

        if ($shouldDeleteOriginalFile) {
            $this->assertFileMissing($this->originalTempFileInfo->getPathname());
        } else {
            $this->assertFileExists($this->originalTempFileInfo->getPathname());
        }
    }

    public function provideData(): Iterator
    {
        $smartFileSystem = new SmartFileSystem();

        // source: https://github.com/nette/utils/blob/798f8c1626a8e0e23116d90e588532725cce7d0e/src/Utils/exceptions.php
        $filePathsWithContents = [
            new AddedFileWithContent(
                $this->getFixtureTempDirectory() . '/RegexpException.php',
                $smartFileSystem->readFile(__DIR__ . '/Expected/RegexpException.php')
            ),
            new AddedFileWithContent(
                $this->getFixtureTempDirectory() . '/UnknownImageFileException.php',
                $smartFileSystem->readFile(__DIR__ . '/Expected/UnknownImageFileException.php')
            ),
        ];
        yield [new SmartFileInfo(__DIR__ . '/Source/nette-exceptions.php'), $filePathsWithContents, true];

        $filePathsWithContents = [
            new AddedFileWithContent(
                $this->getFixtureTempDirectory() . '/JustOneExceptionWithoutNamespace.php',
                $smartFileSystem->readFile(__DIR__ . '/Expected/JustOneExceptionWithoutNamespace.php')
            ),
            new AddedFileWithContent(
                $this->getFixtureTempDirectory() . '/JustTwoExceptionWithoutNamespace.php',
                $smartFileSystem->readFile(__DIR__ . '/Expected/JustTwoExceptionWithoutNamespace.php')
            ),
        ];
        yield [new SmartFileInfo(__DIR__ . '/Source/without-namespace.php'), $filePathsWithContents, true];

        $filePathsWithContents = [
            new AddedFileWithContent(
                $this->getFixtureTempDirectory() . '/MyTrait.php',
                $smartFileSystem->readFile(__DIR__ . '/Expected/MyTrait.php')
            ),
            new AddedFileWithContent(
                $this->getFixtureTempDirectory() . '/MyClass.php',
                $smartFileSystem->readFile(__DIR__ . '/Expected/MyClass.php')
            ),
            new AddedFileWithContent(
                $this->getFixtureTempDirectory() . '/MyInterface.php',
                $smartFileSystem->readFile(__DIR__ . '/Expected/MyInterface.php')
            ),
        ];
        yield [new SmartFileInfo(__DIR__ . '/Source/ClassTraitAndInterface.php'), $filePathsWithContents, true];

        // keep original class
        yield [
            new SmartFileInfo(__DIR__ . '/Source/SomeClass.php'),
            // extra files
            [
                new AddedFileWithContent(
                    $this->getFixtureTempDirectory() . '/SomeClass_Exception.php',
                    $smartFileSystem->readFile(__DIR__ . '/Expected/SomeClass_Exception.php')
                ),
            ],
            false,
        ];

        yield [new SmartFileInfo(__DIR__ . '/Fixture/ReadyException.php.inc'), [], false];
    }

    protected function getRectorClass(): string
    {
        return MultipleClassFileToPsr4ClassesRector::class;
    }
}

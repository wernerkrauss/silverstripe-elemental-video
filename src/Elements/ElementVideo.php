<?php

namespace Dynamic\Elements\Video\Elements;

use Override;
use DNADesign\Elemental\Models\BaseElement;
use SilverStripe\Assets\Image;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\FieldType\DBField;
use SilverStripe\ORM\FieldType\DBHTMLText;
use SilverStripe\Assets\File;
use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Forms\Tab;
use SilverStripe\Forms\HTMLEditor\HTMLEditorField;

/**
 * Class ElementVideo
 * @package Dynamic\Elements\Video\Elements
 */
class ElementVideo extends BaseElement
{
    /**
     * @var array
     */
    private static $db = [
        'Content' => 'HTMLText',
        'MediaAspectRatio' => "Enum('21by9,16by9,4by3,1by1','16by9')",
        'Autoplay' => "Enum('Off,On','Off')",
        'Muted' => "Enum('Off,On','On')",
        'Loop' => "Enum('Off,On','Off')",
        'MediaCredits' => 'HTMLText',
    ];

    /**
     * @var array
     */
    private static $has_one = [
        'VideoFileMP4' =>  File::class,
        'VideoFileWEBM' =>  File::class,
        'VideoFileOGV' =>  File::class,
        'PosterImage' => Image::class,
    ];

    private static $defaults = [
        'MediaAspectRatio' => '16by9',
        'Autoplay' => 'Off',
        'Muted' => 'On',
        'Loop' => 'Off',
    ];

    /**
     * @var array
     */
    private static $owns = [
        'VideoFileMP4',
        'VideoFileWEBM',
        'VideoFileOGV',
        'PosterImage',
    ];

    /**
     * @var string
     */
    private static $table_name = 'ElementVideo';

    private static $icon = 'font-icon-block-media';
    private static $singular_name = 'Video';
    private static $plural_name = 'Videos';


    /**
     * @return FieldList
     */
    #[Override]
    public function getCMSFields()
    {
        $this->beforeUpdateCMSFields(function (FieldList $fields): void {

            // Video Tab
            $fields->insertAfter('Main', Tab::create('VideoTab', _t(self::class . '.VideoTab', 'Video')));

            // Video internal / self-hosted Video: MP4
            $fields->removeByName('VideoFileMP4');
            $VideoFileMP4 = UploadField::create('VideoFileMP4', 'Video (.mp4)');
            $VideoFileMP4->setFolderName('Uploads/Elements/Video');
            $VideoFileMP4->getValidator()-> setAllowedExtensions(['mp4']);
            $VideoFileMP4->setDescription('Video in MP4 format');
            $fields->addFieldToTab('Root.VideoTab', $VideoFileMP4);

            // Video internal / self-hosted Video: WEBM
            $fields->removeByName('VideoFileWEBM');
            $VideoFileWEBM = UploadField::create('VideoFileWEBM', 'Video (.webm)');
            $VideoFileWEBM -> setFolderName('Uploads/Elements/Video');
            $VideoFileWEBM -> getValidator() -> setAllowedExtensions(['webm']);
            $VideoFileWEBM -> setDescription('Optional Video in WEBM format');
            $fields->addFieldToTab('Root.VideoTab', $VideoFileWEBM);

            // Video internal / self-hosted Video: OGV
            $fields->removeByName('VideoFileOGV');
            $VideoFileOGV = UploadField::create('VideoFileOGV', 'Video (.ogg, .ogv)');
            $VideoFileOGV -> setFolderName('Uploads/Elements/Video');
            $VideoFileOGV -> getValidator() -> setAllowedExtensions(['ogv','ogg']);
            $VideoFileOGV -> setDescription('Optional Video in OGG Theora format');
            $fields->addFieldToTab('Root.VideoTab', $VideoFileOGV);

            // Video: Credits
            $MediaCredits = HTMLEditorField::create('MediaCredits', 'Video Credits');
            $MediaCredits -> setDescription('optional: weiterer Text, unter dem Video angezeigt');
            $fields -> addFieldTotab('Root.VideoTab', $MediaCredits);

            $fields->insertBefore(
                'Content',
                $fields->dataFieldByName('PosterImage')
                    ->setFolderName('Uploads/Elements/Video/PosterImage')
                    ->setAllowedFileCategories('image')
            );
            $fields->dataFieldByName('Content')
                ->setTitle('Content')
                ->setRows(5);
        });

        return parent::getCMSFields();
    }

    /**
     * @return DBHTMLText
     */
    #[Override]
    public function getSummary()
    {
        if ($this->Content) {
            return DBField::create_field('HTMLText', $this->Content)->Summary(20);
        }
    }

    /**
     * @return array
     */
    #[Override]
    protected function provideBlockSchema()
    {
        $blockSchema = parent::provideBlockSchema();
        $blockSchema['content'] = $this->getSummary();
        return $blockSchema;
    }

    /**
     * @return string
     */
    #[Override]
    public function getType()
    {
        return _t(self::class.'.BlockType', 'Video');
    }
}

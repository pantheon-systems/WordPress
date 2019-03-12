<?php
/**
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */


if ( !defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

if ( !class_exists( 'YIT_Icons' ) ) {
    /**
     * YIT Icons
     *
     * Class to manage icons
     *
     * @class       YIT_Icons
     * @package     YITH
     * @since       1.0.0
     * @author      Leanza Francesco <leanzafrancesco@gmail.com>
     *
     */
    class YIT_Icons {

        /**
         * Single instance of the class
         *
         * @var YIT_Icons
         */
        private static $_instance;

        /**
         * @var array
         */
        private $_data
            = array(
                'font_awesome_version' => '4.6.3',
                'icons'                => array(
                    'FontAwesome' => array(
                        '\f000' => 'glass',
                        '\f001' => 'music',
                        '\f002' => 'search',
                        '\f003' => 'envelope-o',
                        '\f004' => 'heart',
                        '\f005' => 'star',
                        '\f006' => 'star-o',
                        '\f007' => 'user',
                        '\f008' => 'film',
                        '\f009' => 'th-large',
                        '\f00a' => 'th',
                        '\f00b' => 'th-list',
                        '\f00c' => 'check',
                        '\f00d' => 'times',
                        '\f00e' => 'search-plus',
                        '\f010' => 'search-minus',
                        '\f011' => 'power-off',
                        '\f012' => 'signal',
                        '\f013' => 'cog',
                        '\f014' => 'trash-o',
                        '\f015' => 'home',
                        '\f016' => 'file-o',
                        '\f017' => 'clock-o',
                        '\f018' => 'road',
                        '\f019' => 'download',
                        '\f01a' => 'arrow-circle-o-down',
                        '\f01b' => 'arrow-circle-o-up',
                        '\f01c' => 'inbox',
                        '\f01d' => 'play-circle-o',
                        '\f01e' => 'repeat',
                        '\f021' => 'refresh',
                        '\f022' => 'list-alt',
                        '\f023' => 'lock',
                        '\f024' => 'flag',
                        '\f025' => 'headphones',
                        '\f026' => 'volume-off',
                        '\f027' => 'volume-down',
                        '\f028' => 'volume-up',
                        '\f029' => 'qrcode',
                        '\f02a' => 'barcode',
                        '\f02b' => 'tag',
                        '\f02c' => 'tags',
                        '\f02d' => 'book',
                        '\f02e' => 'bookmark',
                        '\f02f' => 'print',
                        '\f030' => 'camera',
                        '\f031' => 'font',
                        '\f032' => 'bold',
                        '\f033' => 'italic',
                        '\f034' => 'text-height',
                        '\f035' => 'text-width',
                        '\f036' => 'align-left',
                        '\f037' => 'align-center',
                        '\f038' => 'align-right',
                        '\f039' => 'align-justify',
                        '\f03a' => 'list',
                        '\f03b' => 'outdent',
                        '\f03c' => 'indent',
                        '\f03d' => 'video-camera',
                        '\f03e' => 'picture-o',
                        '\f040' => 'pencil',
                        '\f041' => 'map-marker',
                        '\f042' => 'adjust',
                        '\f043' => 'tint',
                        '\f044' => 'pencil-square-o',
                        '\f045' => 'share-square-o',
                        '\f046' => 'check-square-o',
                        '\f047' => 'arrows',
                        '\f048' => 'step-backward',
                        '\f049' => 'fast-backward',
                        '\f04a' => 'backward',
                        '\f04b' => 'play',
                        '\f04c' => 'pause',
                        '\f04d' => 'stop',
                        '\f04e' => 'forward',
                        '\f050' => 'fast-forward',
                        '\f051' => 'step-forward',
                        '\f052' => 'eject',
                        '\f053' => 'chevron-left',
                        '\f054' => 'chevron-right',
                        '\f055' => 'plus-circle',
                        '\f056' => 'minus-circle',
                        '\f057' => 'times-circle',
                        '\f058' => 'check-circle',
                        '\f059' => 'question-circle',
                        '\f05a' => 'info-circle',
                        '\f05b' => 'crosshairs',
                        '\f05c' => 'times-circle-o',
                        '\f05d' => 'check-circle-o',
                        '\f05e' => 'ban',
                        '\f060' => 'arrow-left',
                        '\f061' => 'arrow-right',
                        '\f062' => 'arrow-up',
                        '\f063' => 'arrow-down',
                        '\f064' => 'share',
                        '\f065' => 'expand',
                        '\f066' => 'compress',
                        '\f067' => 'plus',
                        '\f068' => 'minus',
                        '\f069' => 'asterisk',
                        '\f06a' => 'exclamation-circle',
                        '\f06b' => 'gift',
                        '\f06c' => 'leaf',
                        '\f06d' => 'fire',
                        '\f06e' => 'eye',
                        '\f070' => 'eye-slash',
                        '\f071' => 'exclamation-triangle',
                        '\f072' => 'plane',
                        '\f073' => 'calendar',
                        '\f074' => 'random',
                        '\f075' => 'comment',
                        '\f076' => 'magnet',
                        '\f077' => 'chevron-up',
                        '\f078' => 'chevron-down',
                        '\f079' => 'retweet',
                        '\f07a' => 'shopping-cart',
                        '\f07b' => 'folder',
                        '\f07c' => 'folder-open',
                        '\f07d' => 'arrows-v',
                        '\f07e' => 'arrows-h',
                        '\f080' => 'bar-chart',
                        '\f081' => 'twitter-square',
                        '\f082' => 'facebook-square',
                        '\f083' => 'camera-retro',
                        '\f084' => 'key',
                        '\f085' => 'cogs',
                        '\f086' => 'comments',
                        '\f087' => 'thumbs-o-up',
                        '\f088' => 'thumbs-o-down',
                        '\f089' => 'star-half',
                        '\f08a' => 'heart-o',
                        '\f08b' => 'sign-out',
                        '\f08c' => 'linkedin-square',
                        '\f08d' => 'thumb-tack',
                        '\f08e' => 'external-link',
                        '\f090' => 'sign-in',
                        '\f091' => 'trophy',
                        '\f092' => 'github-square',
                        '\f093' => 'upload',
                        '\f094' => 'lemon-o',
                        '\f095' => 'phone',
                        '\f096' => 'square-o',
                        '\f097' => 'bookmark-o',
                        '\f098' => 'phone-square',
                        '\f099' => 'twitter',
                        '\f09a' => 'facebook',
                        '\f09b' => 'github',
                        '\f09c' => 'unlock',
                        '\f09d' => 'credit-card',
                        '\f09e' => 'rss',
                        '\f0a0' => 'hdd-o',
                        '\f0a1' => 'bullhorn',
                        '\f0a2' => 'bell-o',
                        '\f0a3' => 'certificate',
                        '\f0a4' => 'hand-o-right',
                        '\f0a5' => 'hand-o-left',
                        '\f0a6' => 'hand-o-up',
                        '\f0a7' => 'hand-o-down',
                        '\f0a8' => 'arrow-circle-left',
                        '\f0a9' => 'arrow-circle-right',
                        '\f0aa' => 'arrow-circle-up',
                        '\f0ab' => 'arrow-circle-down',
                        '\f0ac' => 'globe',
                        '\f0ad' => 'wrench',
                        '\f0ae' => 'tasks',
                        '\f0b0' => 'filter',
                        '\f0b1' => 'briefcase',
                        '\f0b2' => 'arrows-alt',
                        '\f0c0' => 'users',
                        '\f0c1' => 'link',
                        '\f0c2' => 'cloud',
                        '\f0c3' => 'flask',
                        '\f0c4' => 'scissors',
                        '\f0c5' => 'files-o',
                        '\f0c6' => 'paperclip',
                        '\f0c7' => 'floppy-o',
                        '\f0c8' => 'square',
                        '\f0c9' => 'bars',
                        '\f0ca' => 'list-ul',
                        '\f0cb' => 'list-ol',
                        '\f0cc' => 'strikethrough',
                        '\f0cd' => 'underline',
                        '\f0ce' => 'table',
                        '\f0d0' => 'magic',
                        '\f0d1' => 'truck',
                        '\f0d2' => 'pinterest',
                        '\f0d3' => 'pinterest-square',
                        '\f0d4' => 'google-plus-square',
                        '\f0d5' => 'google-plus',
                        '\f0d6' => 'money',
                        '\f0d7' => 'caret-down',
                        '\f0d8' => 'caret-up',
                        '\f0d9' => 'caret-left',
                        '\f0da' => 'caret-right',
                        '\f0db' => 'columns',
                        '\f0dc' => 'sort',
                        '\f0dd' => 'sort-desc',
                        '\f0de' => 'sort-asc',
                        '\f0e0' => 'envelope',
                        '\f0e1' => 'linkedin',
                        '\f0e2' => 'undo',
                        '\f0e3' => 'gavel',
                        '\f0e4' => 'tachometer',
                        '\f0e5' => 'comment-o',
                        '\f0e6' => 'comments-o',
                        '\f0e7' => 'bolt',
                        '\f0e8' => 'sitemap',
                        '\f0e9' => 'umbrella',
                        '\f0ea' => 'clipboard',
                        '\f0eb' => 'lightbulb-o',
                        '\f0ec' => 'exchange',
                        '\f0ed' => 'cloud-download',
                        '\f0ee' => 'cloud-upload',
                        '\f0f0' => 'user-md',
                        '\f0f1' => 'stethoscope',
                        '\f0f2' => 'suitcase',
                        '\f0f3' => 'bell',
                        '\f0f4' => 'coffee',
                        '\f0f5' => 'cutlery',
                        '\f0f6' => 'file-text-o',
                        '\f0f7' => 'building-o',
                        '\f0f8' => 'hospital-o',
                        '\f0f9' => 'ambulance',
                        '\f0fa' => 'medkit',
                        '\f0fb' => 'fighter-jet',
                        '\f0fc' => 'beer',
                        '\f0fd' => 'h-square',
                        '\f0fe' => 'plus-square',
                        '\f100' => 'angle-double-left',
                        '\f101' => 'angle-double-right',
                        '\f102' => 'angle-double-up',
                        '\f103' => 'angle-double-down',
                        '\f104' => 'angle-left',
                        '\f105' => 'angle-right',
                        '\f106' => 'angle-up',
                        '\f107' => 'angle-down',
                        '\f108' => 'desktop',
                        '\f109' => 'laptop',
                        '\f10a' => 'tablet',
                        '\f10b' => 'mobile',
                        '\f10c' => 'circle-o',
                        '\f10d' => 'quote-left',
                        '\f10e' => 'quote-right',
                        '\f110' => 'spinner',
                        '\f111' => 'circle',
                        '\f112' => 'reply',
                        '\f113' => 'github-alt',
                        '\f114' => 'folder-o',
                        '\f115' => 'folder-open-o',
                        '\f118' => 'smile-o',
                        '\f119' => 'frown-o',
                        '\f11a' => 'meh-o',
                        '\f11b' => 'gamepad',
                        '\f11c' => 'keyboard-o',
                        '\f11d' => 'flag-o',
                        '\f11e' => 'flag-checkered',
                        '\f120' => 'terminal',
                        '\f121' => 'code',
                        '\f122' => 'reply-all',
                        '\f123' => 'star-half-o',
                        '\f124' => 'location-arrow',
                        '\f125' => 'crop',
                        '\f126' => 'code-fork',
                        '\f127' => 'chain-broken',
                        '\f128' => 'question',
                        '\f129' => 'info',
                        '\f12a' => 'exclamation',
                        '\f12b' => 'superscript',
                        '\f12c' => 'subscript',
                        '\f12d' => 'eraser',
                        '\f12e' => 'puzzle-piece',
                        '\f130' => 'microphone',
                        '\f131' => 'microphone-slash',
                        '\f132' => 'shield',
                        '\f133' => 'calendar-o',
                        '\f134' => 'fire-extinguisher',
                        '\f135' => 'rocket',
                        '\f136' => 'maxcdn',
                        '\f137' => 'chevron-circle-left',
                        '\f138' => 'chevron-circle-right',
                        '\f139' => 'chevron-circle-up',
                        '\f13a' => 'chevron-circle-down',
                        '\f13b' => 'html5',
                        '\f13c' => 'css3',
                        '\f13d' => 'anchor',
                        '\f13e' => 'unlock-alt',
                        '\f140' => 'bullseye',
                        '\f141' => 'ellipsis-h',
                        '\f142' => 'ellipsis-v',
                        '\f143' => 'rss-square',
                        '\f144' => 'play-circle',
                        '\f145' => 'ticket',
                        '\f146' => 'minus-square',
                        '\f147' => 'minus-square-o',
                        '\f148' => 'level-up',
                        '\f149' => 'level-down',
                        '\f14a' => 'check-square',
                        '\f14b' => 'pencil-square',
                        '\f14c' => 'external-link-square',
                        '\f14d' => 'share-square',
                        '\f14e' => 'compass',
                        '\f150' => 'caret-square-o-down',
                        '\f151' => 'caret-square-o-up',
                        '\f152' => 'caret-square-o-right',
                        '\f153' => 'eur',
                        '\f154' => 'gbp',
                        '\f155' => 'usd',
                        '\f156' => 'inr',
                        '\f157' => 'jpy',
                        '\f158' => 'rub',
                        '\f159' => 'krw',
                        '\f15a' => 'btc',
                        '\f15b' => 'file',
                        '\f15c' => 'file-text',
                        '\f15d' => 'sort-alpha-asc',
                        '\f15e' => 'sort-alpha-desc',
                        '\f160' => 'sort-amount-asc',
                        '\f161' => 'sort-amount-desc',
                        '\f162' => 'sort-numeric-asc',
                        '\f163' => 'sort-numeric-desc',
                        '\f164' => 'thumbs-up',
                        '\f165' => 'thumbs-down',
                        '\f166' => 'youtube-square',
                        '\f167' => 'youtube',
                        '\f168' => 'xing',
                        '\f169' => 'xing-square',
                        '\f16a' => 'youtube-play',
                        '\f16b' => 'dropbox',
                        '\f16c' => 'stack-overflow',
                        '\f16d' => 'instagram',
                        '\f16e' => 'flickr',
                        '\f170' => 'adn',
                        '\f171' => 'bitbucket',
                        '\f172' => 'bitbucket-square',
                        '\f173' => 'tumblr',
                        '\f174' => 'tumblr-square',
                        '\f175' => 'long-arrow-down',
                        '\f176' => 'long-arrow-up',
                        '\f177' => 'long-arrow-left',
                        '\f178' => 'long-arrow-right',
                        '\f179' => 'apple',
                        '\f17a' => 'windows',
                        '\f17b' => 'android',
                        '\f17c' => 'linux',
                        '\f17d' => 'dribbble',
                        '\f17e' => 'skype',
                        '\f180' => 'foursquare',
                        '\f181' => 'trello',
                        '\f182' => 'female',
                        '\f183' => 'male',
                        '\f184' => 'gratipay',
                        '\f185' => 'sun-o',
                        '\f186' => 'moon-o',
                        '\f187' => 'archive',
                        '\f188' => 'bug',
                        '\f189' => 'vk',
                        '\f18a' => 'weibo',
                        '\f18b' => 'renren',
                        '\f18c' => 'pagelines',
                        '\f18d' => 'stack-exchange',
                        '\f18e' => 'arrow-circle-o-right',
                        '\f190' => 'arrow-circle-o-left',
                        '\f191' => 'caret-square-o-left',
                        '\f192' => 'dot-circle-o',
                        '\f193' => 'wheelchair',
                        '\f194' => 'vimeo-square',
                        '\f195' => 'try',
                        '\f196' => 'plus-square-o',
                        '\f197' => 'space-shuttle',
                        '\f198' => 'slack',
                        '\f199' => 'envelope-square',
                        '\f19a' => 'wordpress',
                        '\f19b' => 'openid',
                        '\f19c' => 'university',
                        '\f19d' => 'graduation-cap',
                        '\f19e' => 'yahoo',
                        '\f1a0' => 'google',
                        '\f1a1' => 'reddit',
                        '\f1a2' => 'reddit-square',
                        '\f1a3' => 'stumbleupon-circle',
                        '\f1a4' => 'stumbleupon',
                        '\f1a5' => 'delicious',
                        '\f1a6' => 'digg',
                        '\f1a7' => 'pied-piper-pp',
                        '\f1a8' => 'pied-piper-alt',
                        '\f1a9' => 'drupal',
                        '\f1aa' => 'joomla',
                        '\f1ab' => 'language',
                        '\f1ac' => 'fax',
                        '\f1ad' => 'building',
                        '\f1ae' => 'child',
                        '\f1b0' => 'paw',
                        '\f1b1' => 'spoon',
                        '\f1b2' => 'cube',
                        '\f1b3' => 'cubes',
                        '\f1b4' => 'behance',
                        '\f1b5' => 'behance-square',
                        '\f1b6' => 'steam',
                        '\f1b7' => 'steam-square',
                        '\f1b8' => 'recycle',
                        '\f1b9' => 'car',
                        '\f1ba' => 'taxi',
                        '\f1bb' => 'tree',
                        '\f1bc' => 'spotify',
                        '\f1bd' => 'deviantart',
                        '\f1be' => 'soundcloud',
                        '\f1c0' => 'database',
                        '\f1c1' => 'file-pdf-o',
                        '\f1c2' => 'file-word-o',
                        '\f1c3' => 'file-excel-o',
                        '\f1c4' => 'file-powerpoint-o',
                        '\f1c5' => 'file-image-o',
                        '\f1c6' => 'file-archive-o',
                        '\f1c7' => 'file-audio-o',
                        '\f1c8' => 'file-video-o',
                        '\f1c9' => 'file-code-o',
                        '\f1ca' => 'vine',
                        '\f1cb' => 'codepen',
                        '\f1cc' => 'jsfiddle',
                        '\f1cd' => 'life-ring',
                        '\f1ce' => 'circle-o-notch',
                        '\f1d0' => 'rebel',
                        '\f1d1' => 'empire',
                        '\f1d2' => 'git-square',
                        '\f1d3' => 'git',
                        '\f1d4' => 'hacker-news',
                        '\f1d5' => 'tencent-weibo',
                        '\f1d6' => 'qq',
                        '\f1d7' => 'weixin',
                        '\f1d8' => 'paper-plane',
                        '\f1d9' => 'paper-plane-o',
                        '\f1da' => 'history',
                        '\f1db' => 'circle-thin',
                        '\f1dc' => 'header',
                        '\f1dd' => 'paragraph',
                        '\f1de' => 'sliders',
                        '\f1e0' => 'share-alt',
                        '\f1e1' => 'share-alt-square',
                        '\f1e2' => 'bomb',
                        '\f1e3' => 'futbol-o',
                        '\f1e4' => 'tty',
                        '\f1e5' => 'binoculars',
                        '\f1e6' => 'plug',
                        '\f1e7' => 'slideshare',
                        '\f1e8' => 'twitch',
                        '\f1e9' => 'yelp',
                        '\f1ea' => 'newspaper-o',
                        '\f1eb' => 'wifi',
                        '\f1ec' => 'calculator',
                        '\f1ed' => 'paypal',
                        '\f1ee' => 'google-wallet',
                        '\f1f0' => 'cc-visa',
                        '\f1f1' => 'cc-mastercard',
                        '\f1f2' => 'cc-discover',
                        '\f1f3' => 'cc-amex',
                        '\f1f4' => 'cc-paypal',
                        '\f1f5' => 'cc-stripe',
                        '\f1f6' => 'bell-slash',
                        '\f1f7' => 'bell-slash-o',
                        '\f1f8' => 'trash',
                        '\f1f9' => 'copyright',
                        '\f1fa' => 'at',
                        '\f1fb' => 'eyedropper',
                        '\f1fc' => 'paint-brush',
                        '\f1fd' => 'birthday-cake',
                        '\f1fe' => 'area-chart',
                        '\f200' => 'pie-chart',
                        '\f201' => 'line-chart',
                        '\f202' => 'lastfm',
                        '\f203' => 'lastfm-square',
                        '\f204' => 'toggle-off',
                        '\f205' => 'toggle-on',
                        '\f206' => 'bicycle',
                        '\f207' => 'bus',
                        '\f208' => 'ioxhost',
                        '\f209' => 'angellist',
                        '\f20a' => 'cc',
                        '\f20b' => 'ils',
                        '\f20c' => 'meanpath',
                        '\f20d' => 'buysellads',
                        '\f20e' => 'connectdevelop',
                        '\f210' => 'dashcube',
                        '\f211' => 'forumbee',
                        '\f212' => 'leanpub',
                        '\f213' => 'sellsy',
                        '\f214' => 'shirtsinbulk',
                        '\f215' => 'simplybuilt',
                        '\f216' => 'skyatlas',
                        '\f217' => 'cart-plus',
                        '\f218' => 'cart-arrow-down',
                        '\f219' => 'diamond',
                        '\f21a' => 'ship',
                        '\f21b' => 'user-secret',
                        '\f21c' => 'motorcycle',
                        '\f21d' => 'street-view',
                        '\f21e' => 'heartbeat',
                        '\f221' => 'venus',
                        '\f222' => 'mars',
                        '\f223' => 'mercury',
                        '\f224' => 'transgender',
                        '\f225' => 'transgender-alt',
                        '\f226' => 'venus-double',
                        '\f227' => 'mars-double',
                        '\f228' => 'venus-mars',
                        '\f229' => 'mars-stroke',
                        '\f22a' => 'mars-stroke-v',
                        '\f22b' => 'mars-stroke-h',
                        '\f22c' => 'neuter',
                        '\f22d' => 'genderless',
                        '\f230' => 'facebook-official',
                        '\f231' => 'pinterest-p',
                        '\f232' => 'whatsapp',
                        '\f233' => 'server',
                        '\f234' => 'user-plus',
                        '\f235' => 'user-times',
                        '\f236' => 'bed',
                        '\f237' => 'viacoin',
                        '\f238' => 'train',
                        '\f239' => 'subway',
                        '\f23a' => 'medium',
                        '\f23b' => 'y-combinator',
                        '\f23c' => 'optin-monster',
                        '\f23d' => 'opencart',
                        '\f23e' => 'expeditedssl',
                        '\f240' => 'battery-full',
                        '\f241' => 'battery-three-quarters',
                        '\f242' => 'battery-half',
                        '\f243' => 'battery-quarter',
                        '\f244' => 'battery-empty',
                        '\f245' => 'mouse-pointer',
                        '\f246' => 'i-cursor',
                        '\f247' => 'object-group',
                        '\f248' => 'object-ungroup',
                        '\f249' => 'sticky-note',
                        '\f24a' => 'sticky-note-o',
                        '\f24b' => 'cc-jcb',
                        '\f24c' => 'cc-diners-club',
                        '\f24d' => 'clone',
                        '\f24e' => 'balance-scale',
                        '\f250' => 'hourglass-o',
                        '\f251' => 'hourglass-start',
                        '\f252' => 'hourglass-half',
                        '\f253' => 'hourglass-end',
                        '\f254' => 'hourglass',
                        '\f255' => 'hand-rock-o',
                        '\f256' => 'hand-paper-o',
                        '\f257' => 'hand-scissors-o',
                        '\f258' => 'hand-lizard-o',
                        '\f259' => 'hand-spock-o',
                        '\f25a' => 'hand-pointer-o',
                        '\f25b' => 'hand-peace-o',
                        '\f25c' => 'trademark',
                        '\f25d' => 'registered',
                        '\f25e' => 'creative-commons',
                        '\f260' => 'gg',
                        '\f261' => 'gg-circle',
                        '\f262' => 'tripadvisor',
                        '\f263' => 'odnoklassniki',
                        '\f264' => 'odnoklassniki-square',
                        '\f265' => 'get-pocket',
                        '\f266' => 'wikipedia-w',
                        '\f267' => 'safari',
                        '\f268' => 'chrome',
                        '\f269' => 'firefox',
                        '\f26a' => 'opera',
                        '\f26b' => 'internet-explorer',
                        '\f26c' => 'television',
                        '\f26d' => 'contao',
                        '\f26e' => '500px',
                        '\f270' => 'amazon',
                        '\f271' => 'calendar-plus-o',
                        '\f272' => 'calendar-minus-o',
                        '\f273' => 'calendar-times-o',
                        '\f274' => 'calendar-check-o',
                        '\f275' => 'industry',
                        '\f276' => 'map-pin',
                        '\f277' => 'map-signs',
                        '\f278' => 'map-o',
                        '\f279' => 'map',
                        '\f27a' => 'commenting',
                        '\f27b' => 'commenting-o',
                        '\f27c' => 'houzz',
                        '\f27d' => 'vimeo',
                        '\f27e' => 'black-tie',
                        '\f280' => 'fonticons',
                        '\f281' => 'reddit-alien',
                        '\f282' => 'edge',
                        '\f283' => 'credit-card-alt',
                        '\f284' => 'codiepie',
                        '\f285' => 'modx',
                        '\f286' => 'fort-awesome',
                        '\f287' => 'usb',
                        '\f288' => 'product-hunt',
                        '\f289' => 'mixcloud',
                        '\f28a' => 'scribd',
                        '\f28b' => 'pause-circle',
                        '\f28c' => 'pause-circle-o',
                        '\f28d' => 'stop-circle',
                        '\f28e' => 'stop-circle-o',
                        '\f290' => 'shopping-bag',
                        '\f291' => 'shopping-basket',
                        '\f292' => 'hashtag',
                        '\f293' => 'bluetooth',
                        '\f294' => 'bluetooth-b',
                        '\f295' => 'percent',
                        '\f296' => 'gitlab',
                        '\f297' => 'wpbeginner',
                        '\f298' => 'wpforms',
                        '\f299' => 'envira',
                        '\f29a' => 'universal-access',
                        '\f29b' => 'wheelchair-alt',
                        '\f29c' => 'question-circle-o',
                        '\f29d' => 'blind',
                        '\f29e' => 'audio-description',
                        '\f2a0' => 'volume-control-phone',
                        '\f2a1' => 'braille',
                        '\f2a2' => 'assistive-listening-systems',
                        '\f2a3' => 'american-sign-language-interpreting',
                        '\f2a4' => 'deaf',
                        '\f2a5' => 'glide',
                        '\f2a6' => 'glide-g',
                        '\f2a7' => 'sign-language',
                        '\f2a8' => 'low-vision',
                        '\f2a9' => 'viadeo',
                        '\f2aa' => 'viadeo-square',
                        '\f2ab' => 'snapchat',
                        '\f2ac' => 'snapchat-ghost',
                        '\f2ad' => 'snapchat-square',
                        '\f2ae' => 'pied-piper',
                        '\f2b0' => 'first-order',
                        '\f2b1' => 'yoast',
                        '\f2b2' => 'themeisle',
                        '\f2b3' => 'google-plus-official',
                        '\f2b4' => 'font-awesome',
                    ),
                    'Dashicons'   => array(
                        '\f100' => 'admin-appearance',
                        '\f101' => 'admin-comments',
                        '\f102' => 'admin-home',
                        '\f104' => 'admin-media',
                        '\f105' => 'admin-page',
                        '\f106' => 'admin-plugins',
                        '\f107' => 'admin-tools',
                        '\f108' => 'admin-settings',
                        '\f110' => 'admin-users',
                        '\f111' => 'admin-generic',
                        '\f112' => 'admin-network',
                        '\f115' => 'welcome-view-site',
                        '\f116' => 'welcome-widgets-menus',
                        '\f117' => 'welcome-comments',
                        '\f118' => 'welcome-learn-more',
                        '\f120' => 'wordpress',
                        '\f122' => 'format-quote',
                        '\f123' => 'format-aside',
                        '\f125' => 'format-chat',
                        '\f126' => 'format-video',
                        '\f127' => 'format-audio',
                        '\f128' => 'format-image',
                        '\f130' => 'format-status',
                        '\f132' => 'plus',
                        '\f133' => 'welcome-add-page',
                        '\f134' => 'align-center',
                        '\f135' => 'align-left',
                        '\f136' => 'align-right',
                        '\f138' => 'align-none',
                        '\f139' => 'arrow-right',
                        '\f140' => 'arrow-down',
                        '\f141' => 'arrow-left',
                        '\f142' => 'arrow-up',
                        '\f145' => 'calendar',
                        '\f147' => 'yes',
                        '\f148' => 'admin-collapse',
                        '\f153' => 'dismiss',
                        '\f154' => 'star-empty',
                        '\f155' => 'star-filled',
                        '\f156' => 'sort',
                        '\f157' => 'pressthis',
                        '\f158' => 'no',
                        '\f159' => 'marker',
                        '\f160' => 'lock',
                        '\f161' => 'format-gallery',
                        '\f163' => 'list-view',
                        '\f165' => 'image-crop',
                        '\f166' => 'image-rotate-left',
                        '\f167' => 'image-rotate-right',
                        '\f168' => 'image-flip-vertical',
                        '\f169' => 'image-flip-horizontal',
                        '\f171' => 'undo',
                        '\f172' => 'redo',
                        '\f173' => 'post-status',
                        '\f174' => 'cart',
                        '\f175' => 'feedback',
                        '\f176' => 'cloud',
                        '\f177' => 'visibility',
                        '\f178' => 'vault',
                        '\f179' => 'search',
                        '\f180' => 'screenoptions',
                        '\f181' => 'slides',
                        '\f183' => 'analytics',
                        '\f184' => 'chart-pie',
                        '\f185' => 'chart-bar',
                        '\f200' => 'editor-bold',
                        '\f201' => 'editor-italic',
                        '\f203' => 'editor-ul',
                        '\f204' => 'editor-ol',
                        '\f205' => 'editor-quote',
                        '\f206' => 'editor-alignleft',
                        '\f207' => 'editor-aligncenter',
                        '\f208' => 'editor-alignright',
                        '\f209' => 'editor-insertmore',
                        '\f210' => 'editor-spellcheck',
                        '\f212' => 'editor-kitchensink',
                        '\f213' => 'editor-underline',
                        '\f214' => 'editor-justify',
                        '\f215' => 'editor-textcolor',
                        '\f216' => 'editor-paste-word',
                        '\f217' => 'editor-paste-text',
                        '\f218' => 'editor-removeformatting',
                        '\f219' => 'editor-video',
                        '\f220' => 'editor-customchar',
                        '\f221' => 'editor-outdent',
                        '\f222' => 'editor-indent',
                        '\f223' => 'editor-help',
                        '\f224' => 'editor-strikethrough',
                        '\f225' => 'editor-unlink',
                        '\f226' => 'dashboard',
                        '\f227' => 'flag',
                        '\f229' => 'leftright',
                        '\f230' => 'location',
                        '\f231' => 'location-alt',
                        '\f232' => 'images-alt',
                        '\f233' => 'images-alt2',
                        '\f234' => 'video-alt',
                        '\f235' => 'video-alt2',
                        '\f236' => 'video-alt3',
                        '\f237' => 'share1',
                        '\f238' => 'chart-line',
                        '\f239' => 'chart-area',
                        '\f240' => 'share-alt',
                        '\f242' => 'share-alt2',
                        '\f301' => 'twitter',
                        '\f303' => 'rss',
                        '\f304' => 'facebook',
                        '\f305' => 'facebook-alt',
                        '\f306' => 'camera',
                        '\f307' => 'groups',
                        '\f308' => 'hammer',
                        '\f309' => 'art',
                        '\f310' => 'migrate',
                        '\f311' => 'performance',
                        '\f312' => 'products',
                        '\f313' => 'awards',
                        '\f314' => 'forms',
                        '\f316' => 'download',
                        '\f317' => 'upload',
                        '\f318' => 'category',
                        '\f319' => 'admin-site',
                        '\f320' => 'editor-rtl',
                        '\f321' => 'backup',
                        '\f322' => 'portfolio',
                        '\f323' => 'tag',
                        '\f324' => 'wordpress-alt',
                        '\f325' => 'networking',
                        '\f326' => 'translation',
                        '\f328' => 'smiley',
                        '\f330' => 'book',
                        '\f331' => 'book-alt',
                        '\f332' => 'shield',
                        '\f333' => 'menu',
                        '\f334' => 'shield-alt',
                        '\f335' => 'no-alt',
                        '\f336' => 'id',
                        '\f337' => 'id-alt',
                        '\f338' => 'businessman',
                        '\f339' => 'lightbulb',
                        '\f340' => 'arrow-left-alt',
                        '\f341' => 'arrow-left-alt2',
                        '\f342' => 'arrow-up-alt',
                        '\f343' => 'arrow-up-alt2',
                        '\f344' => 'arrow-right-alt',
                        '\f345' => 'arrow-right-alt2',
                        '\f346' => 'arrow-down-alt',
                        '\f347' => 'arrow-down-alt2',
                        '\f348' => 'info',
                        '\f459' => 'star-half',
                        '\f460' => 'minus',
                        '\f462' => 'googleplus',
                        '\f463' => 'update',
                        '\f464' => 'edit',
                        '\f465' => 'email',
                        '\f466' => 'email-alt',
                        '\f468' => 'sos',
                        '\f469' => 'clock',
                        '\f470' => 'smartphone',
                        '\f471' => 'tablet',
                        '\f472' => 'desktop',
                        '\f473' => 'testimonial',
                        '\f474' => 'editor-break',
                        '\f475' => 'editor-code',
                        '\f476' => 'editor-paragraph',
                        '\f478' => 'text',
                        '\f479' => 'tagcloud',
                        '\f480' => 'archive',
                        '\f481' => 'clipboard',
                        '\f482' => 'microphone',
                        '\f483' => 'universal-access',
                        '\f484' => 'nametag',
                        '\f486' => 'tickets',
                        '\f487' => 'heart',
                        '\f488' => 'megaphone',
                        '\f489' => 'schedule',
                        '\f490' => 'media-video',
                        '\f491' => 'media-text',
                        '\f492' => 'playlist-audio',
                        '\f493' => 'playlist-video',
                        '\f495' => 'media-spreadsheet',
                        '\f496' => 'media-interactive',
                        '\f497' => 'media-document',
                        '\f498' => 'media-default',
                        '\f499' => 'media-code',
                        '\f500' => 'media-audio',
                        '\f501' => 'media-archive',
                        '\f502' => 'plus-alt',
                        '\f503' => 'randomize',
                        '\f504' => 'external',
                        '\f506' => 'editor-contract',
                        '\f507' => 'universal-access-alt',
                        '\f508' => 'calendar-alt',
                        '\f509' => 'grid-view',
                        '\f510' => 'index-card',
                        '\f511' => 'carrot',
                        '\f512' => 'building',
                        '\f513' => 'store',
                        '\f514' => 'album',
                        '\f515' => 'controls-repeat',
                        '\f516' => 'controls-skipback',
                        '\f517' => 'controls-skipforward',
                        '\f518' => 'controls-back',
                        '\f519' => 'controls-forward',
                        '\f520' => 'controls-volumeoff',
                        '\f521' => 'controls-volumeon',
                        '\f522' => 'controls-play',
                        '\f523' => 'controls-pause',
                        '\f524' => 'tickets-alt',
                        '\f525' => 'phone',
                        '\f526' => 'money',
                        '\f527' => 'palmtree',
                        '\f528' => 'unlock',
                        '\f529' => 'thumbs-up',
                        '\f530' => 'hidden',
                        '\f531' => 'image-rotate',
                        '\f533' => 'image-filter',
                        '\f534' => 'warning',
                        '\f535' => 'editor-table',
                        '\f536' => 'filter',
                        '\f537' => 'sticky',
                        '\f538' => 'layout',
                        '\f540' => 'admin-customizer',
                        '\f541' => 'admin-multisite',
                        '\f542' => 'thumbs-down',
                        '\f543' => 'plus-alt2',
                        '\f545' => 'move',
                        '\f546' => 'paperclip',
                        '\f547' => 'laptop',
                    ),
                )
            );


        /**
         * Returns single instance of the class
         *
         * @return YIT_Icons
         * @since 1.0.0
         * @author      Leanza Francesco <leanzafrancesco@gmail.com>
         */
        public static function get_instance() {
            return isset( self::$_instance ) ? self::$_instance : self::$_instance = new self();
        }

        /**
         * @since 1.0.0
         * @access protected
         * @author      Leanza Francesco <leanzafrancesco@gmail.com>
         */
        private function __construct() {
            add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ), 99 );
            add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 99 );
        }

        /**
         * __get function.
         *
         * @param string $key
         *
         * @return mixed
         */
        public function __get( $key ) {
            $value = isset( $this->_data[ $key ] ) ? $this->_data[ $key ] : false;

            return $value;
        }

        /**
         * __isset function.
         *
         * @param string $key
         *
         * @return bool
         */
        public function __isset( $key ) {
            return isset( $this->_data[ $key ] );
        }

        public function get_icons( $filter_icons = '' ) {
            $icons = $this->icons;
            if ( !empty( $filter_icons ) ) {
                $icons = apply_filters( 'yith_plugin_fw_icons_field_icons_' . sanitize_key( $filter_icons ), $icons );
            }

            return $icons;
        }

        /**
         * @param        $icon_string
         *
         * @param string $filter_icons
         *
         * @return string
         */
        public function get_icon_data( $icon_string, $filter_icons = '' ) {
            $icon_data = '';
            if ( $icon_string ) {
                $icon_array = explode( ':', $icon_string );

                if ( count( $icon_array ) < 2 ) {
                    return $icon_data;
                }

                $font_name = $icon_array[ 0 ];
                $icon_name = $icon_array[ 1 ];

                $icons = $this->get_icons( $filter_icons );
                if ( array_key_exists( $font_name, $icons ) ) {
                    $icon_key  = array_search( $icon_name, $icons[ $font_name ] );
                    $icon_code = '&#x' . str_replace( '\\', '', $icon_key );

                    $icon_data = 'data-font="' . esc_attr( $font_name ) . '" data-name="' . esc_attr( $icon_name ) . '" data-key="' . esc_attr( $icon_key ) . '" data-icon="' . $icon_code . '"';
                }
            }

            return $icon_data;
        }

        /**
         * Retrieves the font awesome array
         * the first time retrieves the array by the Font Awesome CSS
         * [utility method]
         *
         * @return array
         */
        private function _get_font_awesome_array() {
            $font_awesome_list    = get_option( 'yit_font_awesome_list', array() );
            $font_awesome_version = $this->font_awesome_version;

            $font_awesome_array = array();

            if ( !empty( $font_awesome_list[ $font_awesome_version ] ) ) {
                $font_awesome_array = $font_awesome_list[ $font_awesome_version ];
            } else {
                // Create the array by css
                $font_awesome_file = "https://maxcdn.bootstrapcdn.com/font-awesome/{$font_awesome_version}/css/font-awesome.min.css";

                $pattern = '/\.(fa-(?:\w+(?:-)?)+):before{content:"\\\\(....)"}/';
                $subject = file_get_contents( $font_awesome_file );
                preg_match_all( $pattern, $subject, $matches, PREG_SET_ORDER );
                foreach ( $matches as $match ) {
                    $font_awesome_array[ $match[ 2 ] ] = $match[ 1 ];
                }
                ksort( $font_awesome_array );

                $font_awesome_list[ $font_awesome_version ] = $font_awesome_array;
                update_option( 'yit_font_awesome_list', $font_awesome_list );
            }

            return $font_awesome_array;
        }

        /**
         * Retrieves the dashicons array
         * the first time retrieves the array by the Dashicons CSS
         * [utility method]
         *
         * @return array
         */
        private function _get_dashicons_array() {
            $dashicons_list = get_option( 'yit_dashicons_list', array() );

            $dashicons_array = array();
            if ( false && !empty( $dashicons_list ) ) {
                $dashicons_array = $dashicons_list;
            } else {
                // Create the array by css
                $file = "https://s.w.org/wp-includes/css/dashicons.min.css";

                $pattern = '/\.dashicons-((?:\w+(?:-)?)+):before{content:"\\\\(....)"/';
                $pattern = '/\.dashicons-((?:\w+(?:-)?)+):before.............../';
                $subject = file_get_contents( $file );
                preg_match_all( $pattern, $subject, $matches, PREG_SET_ORDER );

                foreach ( $matches as $match ) {
                    $code = str_replace( '.dashicons-' . $match[ 1 ] . ':before{content:"\\', '', $match[ 0 ] );
                    if ( strlen( $code ) == 4 )
                        $dashicons_array[ $code ] = $match[ 1 ];
                }
                ksort( $dashicons_array );
            }

            return $dashicons_array;
        }

        /**
         * Enqueue Scripts
         *
         * @author      Leanza Francesco <leanzafrancesco@gmail.com>
         */
        public function enqueue_scripts() {
            wp_register_style( 'font-awesome', "https://maxcdn.bootstrapcdn.com/font-awesome/{$this->font_awesome_version}/css/font-awesome.min.css", array(), $this->font_awesome_version );

            $font_awesome_inline = $this->get_inline_scripts( 'font-awesome' );
            wp_add_inline_style( 'font-awesome', $font_awesome_inline );

            $dashicons_inline = $this->get_inline_scripts( 'dashicons' );
            wp_add_inline_style( 'dashicons', $dashicons_inline );
        }

        /**
         * Return the icon HTML from icon_string passed
         *
         * @param string|array $icon
         * @param array        $args
         *
         * @return string
         */
        public function get_icon( $icon = '', $args = array() ) {
            $icon      = is_array( $icon ) && isset( $icon[ 'icon' ] ) ? $icon[ 'icon' ] : $icon;
            $icon_html = "";
            if ( $icon ) {
                $default_args = array(
                    'html_tag'     => 'span',
                    'class'        => '',
                    'style'        => '',
                    'filter_icons' => ''
                );
                $args         = wp_parse_args( $args, $default_args );
                /**
                 * @var string $html_tag
                 * @var string $class
                 * @var string $style
                 * @var string $filter_icons
                 */
                extract( $args );

                if ( $icon_data = $this->get_icon_data( $icon, $filter_icons ) ) {
                    $icon_html = "<$html_tag class=\"$class\" style=\"$style\" $icon_data></$html_tag>";
                }

            }

            return $icon_html;
        }

        /**
         * Get inline scripts
         *
         * @param $slug
         *
         * @return string
         */
        public function get_inline_scripts( $slug ) {
            $css = '';
            switch ( $slug ) {
                case 'font-awesome':
                    $css = '[data-font="FontAwesome"]:before {font-family: \'FontAwesome\' !important;content: attr(data-icon) !important;speak: none !important;font-weight: normal !important;font-variant: normal !important;text-transform: none !important;line-height: 1 !important;font-style: normal !important;-webkit-font-smoothing: antialiased !important;-moz-osx-font-smoothing: grayscale !important;}';
                    break;
                case 'dashicons':
                    $css = '[data-font="Dashicons"]:before {font-family: \'Dashicons\' !important;content: attr(data-icon) !important;speak: none !important;font-weight: normal !important;font-variant: normal !important;text-transform: none !important;line-height: 1 !important;font-style: normal !important;-webkit-font-smoothing: antialiased !important;-moz-osx-font-smoothing: grayscale !important;}';
            }

            return $css;
        }

    }
}
function YIT_Icons() {
    return YIT_Icons::get_instance();
}

YIT_Icons();
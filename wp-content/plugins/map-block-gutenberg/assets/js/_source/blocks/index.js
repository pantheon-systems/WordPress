/**
 * Block dependencies
 */

 
import classnames from 'classnames';
import Inspector from './inspector';
import Controls from './controls';
import icon from './icon';

const { __ } = wp.i18n;
const {
    registerBlockType,
} = wp.blocks;
const {
    RichText,
} = wp.editor;

function getSettings( attributes ) {
    let settings = [];
    for( let attribute in attributes ) {
        let value = attributes[ attribute ];
        if( 'boolean' === typeof attributes[ attribute ] ) {
            value = value.toString();
        }
        settings.push( <li>{ attribute }: { value }</li> );
    }
    return settings;
}

function buildMapIframe( attributes ) {
    return <div class="wp-block-webfactory-map"><iframe width='100%' height={parseInt(attributes.height, 10) + 'px'} src={'https://www.google.com/maps/embed/v1/place?q=' + encodeURIComponent(attributes.address) + '&maptype=roadmap&zoom=' + parseInt(attributes.zoom, 10) + '&key=' + attributes.api_key} frameBorder='0'></iframe></div>
} // buildMapIframe

/**
 * Register static block example block
 */
export default registerBlockType(
    'webfactory/map',
    {
        title: wf_map_block._map,
        description: wf_map_block._description,
        category: 'common',        
        icon,         
        keywords: [
            wf_map_block._map_lc,
            wf_map_block._location_lc,
            'google',
        ],
        attributes: {
            zoom: {
                type: 'number',
                default: '10',
            },
            height: {
                type: 'number',
                default: '300',
            },
            address: {
                type: 'string',
                default: 'Theater District, New York, USA',
            },    
            api_key: {
                type: 'string',
                default: wf_map_block.api_key,
            }
        },
        edit: props => {
            const { attributes: { message },
                attributes, className, setAttributes } = props;

            let maphtml = buildMapIframe( attributes );

            return [
                <Inspector { ...{ setAttributes, ...props} } />,
                <div>
                    { maphtml }                    
                </div>
            ];
        },
        save: props => {
            const { attributes } = props;

            let maphtml = buildMapIframe( attributes );

            return(
                <div>                  
                    { maphtml }
                </div>
            );
        },
    },
);

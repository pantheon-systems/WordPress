/**
 * Internal block libraries
 */

import throttle from 'lodash.throttle';

const { Component } = wp.element;
const {
    InspectorControls,
    ColorPalette,
} = wp.editor;
const {
    Button,
    ButtonGroup,
    CheckboxControl,
    PanelBody,
    PanelRow,
    PanelColor,
    RadioControl,
    RangeControl,
    TextControl,
    TextareaControl,
    ToggleControl,
    Toolbar,
    SelectControl
} = wp.components;

/**
 * Create an Inspector Controls wrapper Component
 */
export default class Inspector extends Component {

    constructor() {
        super( ...arguments );
        this.updateApiKey = this.updateApiKey.bind(this);  
        this.updateApiKeyThrottled = throttle(this.updateApiKey, 3000);
    }
    
    updateApiKey(key) {
        wf_map_block.api_key = key;

        fetch(ajaxurl, {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: "action=gmw_map_block_save_key&_ajax_nonce=" + wf_map_block.nonce_save_api_key + "&api_key=" + key
        });
    }

    render() {
        const { attributes: { zoom, height, address, api_key }, setAttributes } = this.props;

        return (
            <InspectorControls>
                <PanelBody>
                    <TextControl
                        label={ wf_map_block._address }
                        value={ address }
                        onChange={ address => setAttributes( { address } ) }
                    />
                </PanelBody>
                
                <PanelBody>
                    <RangeControl
                        beforeIcon="arrow-left-alt2"
                        afterIcon="arrow-right-alt2"
                        label={ wf_map_block._zoom }
                        value={ zoom }
                        onChange={ zoom => setAttributes( { zoom } ) }
                        min={ 1 }
                        max={ 21 }
                    />
                </PanelBody>

                <PanelBody>
                    <RangeControl
                        beforeIcon="arrow-left-alt2"
                        afterIcon="arrow-right-alt2"
                        label={ wf_map_block._height }
                        value={ height }
                        onChange={ height => setAttributes( { height } ) }
                        min={ 50 }
                        max={ 1000 }
                    />
                </PanelBody>

                <PanelBody>
                    <TextControl
                        label={ wf_map_block._api_key }
                        help={ <p>{wf_map_block._api_info_start} <a href="https://console.developers.google.com" target="_blank">{wf_map_block._api_info_console}</a>. {wf_map_block._api_info_end}</p> }
                        value={ api_key }
                        onChange={ api_key => { 
                            if(!api_key){
                                api_key = 'AIzaSyAjyDspiPfzEfjRSS5fQzm-3jHFjHxeXB4';
                            }
                            setAttributes( { api_key } ); 
                            this.updateApiKeyThrottled( api_key ); 
                        } }
                    />
                </PanelBody>
            </InspectorControls>
        );
    }
}

import { createElement, Component, Fragment } from '@wordpress/element'
import { FocalPointPicker } from '@wordpress/components'
import classnames from 'classnames'
import { __ } from 'ct-i18n'
import _ from 'underscore'

export default class ImageUploader extends Component {
	params = {
		height: 250,
		width: 250,
		flex_width: true,
		flex_height: true,
	}

	state = {
		attachment_info: null,
	}

	getUrlFor = (attachmentInfo) =>
		attachmentInfo
			? (attachmentInfo.width < 700
					? attachmentInfo.sizes.full
					: _.max(
							_.values(
								_.keys(attachmentInfo.sizes).length === 1
									? attachmentInfo.sizes
									: _.omit(attachmentInfo.sizes, 'full')
							),
							({ width }) => width
					  )
			  ).url || attachmentInfo.url
			: null

	onChange = (value, attachment_info = null) =>
		this.props.onChange(
			this.props.option.inline_value
				? value || ''
				: {
						...this.props.value,
						url: this.getUrlFor(attachment_info),
						attachment_id: value,
				  }
		)

	getAttachmentId = (props = this.props) =>
		props.option.inline_value ? props.value : props.value.attachment_id

	/**
	 * Create a media modal select frame, and store it so the instance can be reused when needed.
	 */
	initFrame() {
		this.frame = wp.media({
			button: {
				text: 'Select',
				close: false,
			},
			states: [
				new wp.media.controller.Library({
					title: __('Select logo', 'blocksy'),
					library: wp.media.query({
						type: this.props.option.mediaType || 'image',
					}),
					multiple: false,
					date: false,
					priority: 20,
					suggestedWidth: (this.props.option.logo || {}).width,
					suggestedHeight: (this.props.option.logo || {}).height,
				}),

				...(this.props.option.skipCrop || true
					? []
					: [
							new wp.media.controller.CustomizeImageCropper({
								imgSelectOptions: this
									.calculateImageSelectOptions,
								control: this,
							}),
					  ]),
			],
		})

		this.frame.on('select', this.onSelect, this)
		this.frame.on('close', () => {
			this.props.option.onFrameClose && this.props.option.onFrameClose()
		})
		this.frame.on('cropped', this.onCropped, this)
		this.frame.on('skippedcrop', this.onSkippedCrop, this)
	}

	/**
	 * Open the media modal to the library state.
	 */
	openFrame() {
		this.initFrame()
		this.frame.setState('library').open()
		this.props.option.onFrameOpen && this.props.option.onFrameOpen()
	}

	/**
	 * After an image is selected in the media modal, switch to the cropper
	 * state if the image isn't the right size.
	 */
	onSelect = () => {
		var attachment = this.frame.state().get('selection').first().toJSON()

		if (
			((this.props.option.logo || {}).width === attachment.width &&
				(this.props.option.logo || {}).height === attachment.height &&
				!(this.props.option.logo || {}).flex_width &&
				!(this.props.option.logo || {}).flex_height) ||
			this.props.option.skipCrop ||
			true
		) {
			this.setImageFromAttachment(attachment)
			this.frame.close()
		} else {
			this.frame.setState('cropper')
		}
	}

	/**
	 * After the image has been cropped, apply the cropped image data to the setting.
	 *
	 * @param {object} croppedImage Cropped attachment data.
	 */
	onCropped = (croppedImage) => {
		this.setImageFromAttachment(croppedImage)
	}

	/**
	 * Returns a set of options, computed from the attached image data and
	 * control-specific data, to be fed to the imgAreaSelect plugin in
	 * wp.media.view.Cropper.
	 *
	 * @param {wp.media.model.Attachment} attachment
	 * @param {wp.media.controller.Cropper} controller
	 * @returns {Object} Options
	 */
	calculateImageSelectOptions(attachment, controller) {
		var control = controller.get('control')
		var flexWidth = !!parseInt(
			(control.props.option.logo || {}).flex_width,
			10
		)
		var flexHeight = !!parseInt(
			(control.props.option.logo || {}).flex_height,
			10
		)
		var realWidth = attachment.get('width')
		var realHeight = attachment.get('height')
		var xInit = parseInt((control.props.option.logo || {}).width, 10)
		var yInit = parseInt((control.props.option.logo || {}).height, 10)
		var ratio = xInit / yInit
		var xImg = xInit
		var yImg = yInit
		var x1
		var y1
		var imgSelectOptions

		if (realWidth / realHeight > ratio) {
			yInit = realHeight
			xInit = yInit * ratio
		} else {
			xInit = realWidth
			yInit = xInit / ratio
		}

		x1 = (realWidth - xInit) / 2
		y1 = (realHeight - yInit) / 2

		imgSelectOptions = {
			handles: true,
			keys: true,
			instance: true,
			persistent: true,
			imageWidth: realWidth,
			imageHeight: realHeight,
			minWidth: xImg > xInit ? xInit : xImg,
			minHeight: yImg > yInit ? yInit : yImg,
			x1: x1,
			y1: y1,
			x2: xInit + x1,
			y2: yInit + y1,
		}

		if (flexHeight === false && flexWidth === false) {
			imgSelectOptions.aspectRatio = xInit + ':' + yInit
		}

		if (true === flexHeight) {
			delete imgSelectOptions.minHeight
			imgSelectOptions.maxWidth = realWidth
		}

		if (true === flexWidth) {
			delete imgSelectOptions.minWidth
			imgSelectOptions.maxHeight = realHeight
		}

		return imgSelectOptions
	}

	/**
	 * Return whether the image must be cropped, based on required dimensions.
	 *
	 * @param {bool} flexW
	 * @param {bool} flexH
	 * @param {int}  dstW
	 * @param {int}  dstH
	 * @param {int}  imgW
	 * @param {int}  imgH
	 * @return {bool}
	 */
	mustBeCropped(flexW, flexH, dstW, dstH, imgW, imgH) {
		if (true === flexW && true === flexH) {
			return false
		}

		if (true === flexW && dstH === imgH) {
			return false
		}

		if (true === flexH && dstW === imgW) {
			return false
		}

		if (dstW === imgW && dstH === imgH) {
			return false
		}

		if (imgW <= dstW) {
			return false
		}

		return true
	}

	/**
	 * If cropping was skipped, apply the image data directly to the setting.
	 */
	onSkippedCrop = () => {
		var attachment = this.frame.state().get('selection').first().toJSON()

		this.setImageFromAttachment(attachment)
	}

	/**
	 * Updates the setting and re-renders the control UI.
	 *
	 * @param {object} attachment
	 */
	setImageFromAttachment(attachment) {
		this.onChange(
			attachment.id,
			JSON.parse(
				JSON.stringify(wp.media.attachment(attachment.id).toJSON())
			)
		)

		this.updateAttachmentInfo()
	}

	updateAttachmentInfo = (force = false) => {
		let id = this.getAttachmentId()

		if (!id) return

		if (!wp.media.attachment(id).get('url') || force) {
			wp.media
				.attachment(id)
				.fetch()
				.then(() =>
					this.setState({
						attachment_info: JSON.parse(
							JSON.stringify(wp.media.attachment(id).toJSON())
						),
					})
				)
		} else {
			this.setState({
				attachment_info: JSON.parse(
					JSON.stringify(wp.media.attachment(id).toJSON())
				),
			})
		}

		this.detachListener()
		wp.media.attachment(id).on('change', this.updateAttachmentInfo)
	}

	detachListener() {
		if (!this.getAttachmentId()) return

		wp.media
			.attachment(this.getAttachmentId())
			.off('change', this.updateAttachmentInfo)
	}

	componentDidUpdate(prevProps) {
		if (this.getAttachmentId() !== this.getAttachmentId(prevProps)) {
			wp.media
				.attachment(this.getAttachmentId(prevProps))
				.off('change', this.updateAttachmentInfo)

			this.updateAttachmentInfo()
		}
	}

	componentDidMount() {
		this.updateAttachmentInfo()
	}

	componentWillUnmount() {
		this.detachListener()
	}

	render() {
		return (
			<div
				className={classnames('attachment-media-view ct-attachment', {
					['landscape']:
						this.getAttachmentId() && this.state.attachment_info,
					['attachment-media-view-image']:
						this.getAttachmentId() && this.state.attachment_info,
				})}
				{...(this.props.option.attr || {})}>
				{this.getAttachmentId() && this.state.attachment_info ? (
					<Fragment>
						<div
							className="thumbnail thumbnail-image"
							onClick={() =>
								!this.props.option.has_position_picker &&
								this.openFrame()
							}>
							{!this.props.option.has_position_picker && (
								<img
									className="attachment-thumb"
									src={this.getUrlFor(
										this.state.attachment_info
									)}
									draggable="false"
									alt=""
								/>
							)}

							{this.props.option.has_position_picker && (
								<FocalPointPicker
									url={this.getUrlFor(
										this.state.attachment_info
									)}
									dimensions={{
										width: 400,
										height: 100,
									}}
									value={this.props.value}
									onChange={(drag_position) => {
										this.props.onChange({
											...this.props.value,
											...drag_position,
										})
									}}
								/>
							)}

							<div className="actions">
								<button
									type="button"
									className="button edit-button control-focus"
									title={__('Edit', 'blocksy')}
									onClick={(e) => {
										e.stopPropagation()
										this.openFrame()
									}}
									id="customize-media-control-button-35"></button>
								<button
									onClick={(e) => {
										e.stopPropagation()
										this.setState({ attachment_info: null })
										this.onChange(null)
									}}
									title={__('Remove', 'blocksy')}
									type="button"
									className="button remove-button"></button>
							</div>
						</div>
					</Fragment>
				) : (
					<Fragment>
						<button
							type="button"
							onClick={() => this.openFrame()}
							className="button ct-upload-button"
							id="customize-media-control-button-50">
							{this.props.option.emptyLabel ||
								__('Select logo', 'blocksy')}
						</button>
					</Fragment>
				)}
			</div>
		)
	}
}

console.log('section')

wp.customize.sectionConstructor['blocksySection'] = wp.customize.Section.extend(
	{
		onChangeExpanded: function (expanded, args) {
			return
		},
	}
)

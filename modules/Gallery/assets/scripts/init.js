rs.modules.Gallery =
{
	init: true,
	dependency: typeof window.PhotoSwipe === 'function',
	config:
	{
		selector: 'ul.rs-js-gallery',
		template: 'div.pswp',
		photoswipe:
		{
			index: 0,
			bgOpacity: 0.8
		}
	}
};

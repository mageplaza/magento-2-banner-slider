export function AutoHeightHorizontal( Splide, Components, options) {

    const track = Components.Elements.track;
    const list = Components.Elements.list;

    const defaults = {
        'whileSliding': true,
        'speed': '0.4s',
    }

    let settings = defaults;
    const custom = options.customOptions;

    if (custom) {
        settings.whileSliding = custom.whileSliding ?? defaults.whileSliding;
        settings.speed = custom.speed ?? defaults.speed;
    }

    function mount() {
        const eventType = settings.whileSliding ? 'ready move active resize' : 'ready active resized';
        Splide.on( eventType, adjustHeight );
    }

    function adjustHeight() {

        let element = settings.whileSliding ? track : list;
        let slideHeight = Components.Slides.getAt( typeof( newIndex ) == 'number' ? newIndex : Splide.index ).slide.offsetHeight;

        let trackStyle = track.currentStyle || window.getComputedStyle(track);
        let trackPadding = parseInt(trackStyle.paddingTop) + parseInt(trackStyle.paddingBottom);
        let totalHeight = (settings.whileSliding) ? slideHeight + trackPadding : slideHeight;

        list.style.alignItems = 'flex-start';

        element.style.transition = 'height ' + settings.speed;
        element.style.height = totalHeight + 'px';
    }

    return {
        mount
    };
}

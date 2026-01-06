import Glide from "@glidejs/glide";

document.querySelectorAll('.glide').forEach(carousel =>
    new Glide(carousel, {
        'type': 'carousel',
        'gap': 0,
        'swipeThreshold': 80
    }).mount()
);

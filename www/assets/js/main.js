jQuery(".animated-thumbnails-gallery").justifiedGallery({
	captions: false,
	lastRow: "nojustify",
	rowHeight: 250,
	maxRowHeight: 350,
	margins: 7.5
});



const formInputs = document.querySelectorAll(
	".floating-contact-form .form-container .form-input"
);

const contactIcon = document.querySelector(
	".floating-contact-form .contact-icon"
);

const formContainer = document.querySelector(
	".floating-contact-form .form-container"
);

contactIcon.addEventListener("click", () => {
	formContainer.classList.toggle("active");
});

formInputs.forEach((i) => {
	i.addEventListener("focus", () => {
		i.previousElementSibling.classList.add("active");
	});
});

formInputs.forEach((i) => {
	i.addEventListener("blur", () => {
		if (i.value === "") {
			i.previousElementSibling.classList.remove("active");
		}
	});
});


naja.initialize();
naja.snippetHandler.addEventListener('afterUpdate', (event) => {
    if (event.detail.snippet.id === 'snippet--gallery') {
        jQuery(".animated-thumbnails-gallery").justifiedGallery({
			captions: false,
			lastRow: "nojustify",
			rowHeight: 250,
			maxRowHeight: 350,
			margins: 7.5
		});
    }
});

const INPUT_FILE = document.querySelector('#upload-files');
const INPUT_CONTAINER = document.querySelector('#upload-container');
const FILES_LIST_CONTAINER = document.querySelector('#files-list-container');
const FILE_LIST = [];
let UPLOADED_FILES = [];

const multipleEvents = (element, eventNames, listener) => {
    const events = eventNames.split(' ');

    events.forEach(event => {
        element.addEventListener(event, listener, false);
    });
};

const previewImages = () => {
    FILES_LIST_CONTAINER.innerHTML = '';
    if (FILE_LIST.length > 0) {
        FILE_LIST.forEach((addedFile, index) => {
            const content = `
        <div class="form__image-container js-remove-image" data-index="${index}" data-id="${addedFile.id}" >
          <img class="form__image" src="${addedFile.url}" alt="${addedFile.name}">
        </div>
      `;

            FILES_LIST_CONTAINER.insertAdjacentHTML('beforeEnd', content);
        });
    } else {
        console.log('empty')
        INPUT_FILE.value = "";
    }
}

const fileUpload = () => {
    if (FILES_LIST_CONTAINER) {
        multipleEvents(INPUT_FILE, 'click dragstart dragover', () => {
            INPUT_CONTAINER.classList.add('active');
        });

        multipleEvents(INPUT_FILE, 'dragleave dragend drop change blur', () => {
            INPUT_CONTAINER.classList.remove('active');
        });

        INPUT_FILE.addEventListener('change', () => {
            const files = [...INPUT_FILE.files];
            console.log("changed")
            files.forEach(file => {
                const fileURL = URL.createObjectURL(file);
                const fileName = file.name;
                if (!file.type.match("image/")) {
                    alert(file.name + " is not an image");
                    console.log(file.type)
                } else {
                    const uploadedFiles = {
                        name: fileName,
                        url: fileURL,
                        id: 0,
                    };

                    FILE_LIST.push(uploadedFiles);
                }
            });

            console.log(FILE_LIST)//final list of uploaded files
            previewImages();
            UPLOADED_FILES = document.querySelectorAll(".js-remove-image");
            removeFile();
        });
    }
};

const removeFile = () => {
    UPLOADED_FILES = document.querySelectorAll(".js-remove-image");

    if (UPLOADED_FILES) {
        UPLOADED_FILES.forEach(image => {
            image.addEventListener('click', function () {
                const fileData = this.getAttribute('data-id');
                const fileIndex = this.getAttribute('data-index');
                if (fileData != 0) {
                    Swal.fire({
                        title: "You want delete image ?",
                        text: "Warning you want delete image !!!",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#3085d6",
                        cancelButtonColor: "#d33",
                        confirmButtonText: "ok",
                        cancelButtonText: "cancel",
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                type: "DELETE",
                                url: `/attachment/${fileData}`,
                                headers: {
                                    'X-CSRF-Token': $('meta[name="_token"]').attr('content')
                                },
                                success: function (response) {
                                    Swal.fire({
                                        title: "Delete image success!",
                                        icon: "success",
                                        confirmButtonText: "ok",
                                    }).then((result) => {
                                        FILE_LIST.splice(fileIndex, 1);
                                        previewImages();
                                        removeFile();
                                    });
                                },
                                error: function (error) {
                                    Swal.fire({
                                        title: "Cannot delete image!",
                                        icon: "error",
                                        confirmButtonText: "ok",
                                    });
                                }
                            });

                        }
                    });
                } else {
                    FILE_LIST.splice(fileIndex, 1);
                    previewImages();
                    removeFile();
                }

            });
        });
    } else {
        [...INPUT_FILE.files] = [];
    }
};

fileUpload();
removeFile();

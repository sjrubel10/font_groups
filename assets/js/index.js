$(document).ready(async function () {

    const fileUploadArea = $('#fileUploadArea');
    const fileInput = $('#fileInput');

    fileUploadArea.on('dragover', function (event) {
        event.preventDefault();
        $(this).addClass('dragover');
    });

    fileUploadArea.on('dragleave', function () {
        $(this).removeClass('dragover');
    });

    fileUploadArea.on('drop', function (event) {
        event.preventDefault();
        $(this).removeClass('dragover');
        const files = event.originalEvent.dataTransfer.files; // Access the files
        handleFiles(files);
    });

    // Use mousedown or click event to ensure compatibility
    fileUploadArea.on('mousedown', function (event) {
        if (event.which === 1) { // Ensure left mouse button click
            fileInput.trigger('click'); // Trigger the file input click
        }
    });

    fileInput.on('change', function () {
        const files = this.files; // Get selected files
        handleFiles(files);
    });


    const uploadFile = async (formData, url, action, dataType) => {
        try {
            // Directly await the AJAX call
            const response = await $.ajax({
                url: url,
                type: action,
                data: formData,
                dataType: dataType,
                contentType: false,
                processData: false
            });

            // Return the success response
            return {success: true, data: response};
        } catch (error) {
            // Return an error response
            return {success: false, error: error.statusText || error.message};
        }
    };

    const handleFiles = async (files) => {
        const formData = new FormData();
        for (let i = 0; i < files.length; i++) {
            if (files[i].type === "font/ttf" || files[i].name.endsWith('.ttf')) {
                formData.append('fontFile', files[i]);
            } else {
                $('#uploadStatus').html('<div class="alert alert-danger">Only .ttf files are allowed!</div>');
                return;
            }
        }

        let url = 'upload.php';
        let action = 'POST';
        const result = await uploadFile(formData, url, action, '');
        if (result.success) {
            $('#uploadStatus').html(result.data);

            const get_uploaded_fonts = await fetchFonts('getFonts.php');
            display_uploaded_fonts( get_uploaded_fonts );
        } else {
            $('#uploadStatus').html('<div class="alert alert-danger">File upload failed, please try again. Error: ' + result.error + '</div>');
        }

    }



    var options;
    var loadedFonts;
    const display_uploaded_fonts = (fontFiles) => {
        options = '';
        loadedFonts = [];
        fontFiles.forEach(file => {
            const fontName = file.split('.')[0]; // Extract the font name (without extension)

            loadedFonts.push( fontName );
            // Create a @font-face CSS rule for each font file
            const fontFaceRule = `
                @font-face {
                    font-family: '${fontName}';
                    src: url('uploaded_font/${file}') format('truetype');
                    font-weight: normal;
                    font-style: normal;
                }`;

            // Append the CSS rule to the dynamic font styles
            $('#dynamic-font-styles').append(`<style>${fontFaceRule}</style>`);

            // Create a new div with the loaded font
            const newDiv = `<div style="font-family: '${fontName}'; margin-bottom: 10px;">
                ${fontName}
            </div>`;
            const font_details = `<tr class="uploadedFonts">\
                                            <td>${fontName}</td>\
                                            <td style="font-family: '${fontName}'; margin-bottom: 10px;">${fontName}</td>\
                                            <td id="${fontName}" class="removeLoadedFont">Delete</td>\
                                        </tr>`

            // Append the new div to the font container
            $('#fontHolder').after( font_details );
        });

        if( loadedFonts.length > 0 ){

            $('#fontRows').empty();
            loadedFonts.forEach(function(fontName) {
                options += `<option value="${fontName}">${fontName}</option>`;
            });
            for( let i = 0; i < 2; i++ ) {
                let getStyle = loadSelectFonts( options );
                $('#fontRows').append( getStyle );
            }
        }else{
            alert( 'You Do Not Have Any Uploaded Font Files, Please Upload Font Files For Creating Groups');
        }


    }
    const fetchFonts = async (url) => {
        // $('#uploadedFonts').empty();
        $('#uploadedFontLists').find('.uploadedFonts').remove();
        try {
            // Wrap jQuery AJAX call in a Promise and await the response
            const fontFiles = await $.ajax({
                url: url,
                method: 'GET',
                dataType: 'json'
            });

            return fontFiles;

        } catch (error) {
            console.error('Failed to load font files.', error);
        }
    };

    const data = await fetchFonts('getFonts.php');
    display_uploaded_fonts( data );

    $(document).on('click', '.removeLoadedFont', async function () {
        // Your logic to handle the click event
        let clickedId = $(this).attr('id');
        let fileName = clickedId+'.ttf';

        try {
            const response = await $.ajax({
                url: 'remove_file.php', // PHP script to handle deletion
                type: 'POST',
                data: {filename: fileName},
            });
            $(this).parent().hide();
            $(this).parent().remove();
        } catch (error) {
            $('#uploadStatus').html('<div class="alert alert-danger">Failed to delete the file. Please try again.</div>');
        }
    });

    /*$(document).on('click', 'select[name="font[]"]', function() {
        loadedFonts.forEach(function(fontName) {
            // $("#")<option value="font1">Font 1</option>
        });
    });*/

    //Create Form Group
    var rowCount = 1;
    // let options;
    function loadSelectFonts( options ){
        let formGroupNewRow = `
                     <div class="font-row">
                        <div class="fontDetailsHolder">
                            <div class="form-group">
                                <input type="text" class="form-control" name="title[]" required placeholder="Font name">
                            </div>
                            <div class="form-group">
                                <select class="form-control" name="font_name[]" required>
                                     ${options}
                                </select>
                            </div>
                            <div class="form-group">
                                <input type="number" class="form-control" name="size[]" min="0" required placeholder="1.0">
                            </div>
                            <div class="form-group">
                                <input type="number" class="form-control" name="price[]" min="0" required placeholder="0">
                            </div>
                            <div class="removeRows" >X</div>
                        </div>
                    </div>
                `;
        return formGroupNewRow;

    }

    $(document).on('click', '#addRowBtn', function () {

        let options = '';
        rowCount++;
        if( loadedFonts.length > 0 ){
            loadedFonts.forEach(function(fontName) {
                options += `<option value="${fontName}">${fontName}</option>`;
            });
            let getStyle = loadSelectFonts( options );
            $('.fontRows').append( getStyle );
        }else{
            alert( 'You Do Not Have Any Uploaded Font Files, Please Upload Font Files For Creating Groups');
        }

    });

    // $('#fontRows').append( formGroupNewRow );
    $(document).on('click', '.removeRows', function () {
        if( rowCount > 1 ){
            rowCount--;
            $(this).parent().hide();
            $(this).parent().remove();
        }else{
            alert('At Least One Remain');
        }
    });


    function display_loaded_groups(data) {
        $('#loadGroups').empty();

        $.each(data, function(index, item) {
            var row = '<tr>\
                            <td>' + item.name + '</td>\
                            <td>' + item.font_name + '</td>\
                            <td>' + item.counts + '</td>\
                            <td><span class="editFontGroup" id="edit-'+item.key+'">Edit</span> <span class="deleteFontGroup" id="delete-'+item.key+'">Delete</span></td>\
                        </tr>';

            $('#loadGroups').append(row);
        });
    }

    $('#fontGroupForm').submit(function(event) {
        event.preventDefault();
        const fonts = $('select[name="font_name[]"]').map(function() {
            return $(this).val();
        }).get();
        const uniqueFonts = new Set(fonts);
        if (uniqueFonts.size < 2) {
            $('#validationMessage').html('<div class="alert alert-danger">You must select at least two different fonts.</div>');
            return;
        }
        $('#validationMessage').html('<div class="alert alert-success">Form is valid and submitted to strong.</div>');

        $.ajax({
            url: 'create_group.php',
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                let result= JSON.parse(response);
                // console.log( result.status );
                if ( result.status === 'success') {
                    $('#validationMessage').html('<div class="alert alert-success">Group created successfully.</div>');

                    // console.log( result.data );
                    display_loaded_groups( result.data );
                }else{
                    console.error('Error:', response.message);
                }
            },
            error: function() {
                // Handle error
                $('#validationMessage').html('<div class="alert alert-danger">Failed to create group. Please try again.</div>');
            }
        });


    });

});
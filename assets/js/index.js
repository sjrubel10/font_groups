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

    fileUploadArea.on('mousedown', function (event) {
        if (event.which === 1) {
            fileInput.trigger('click');
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

            return {success: true, data: response};
        } catch (error) {

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

            $('#dynamic-font-styles').append(`<style>${fontFaceRule}</style>`);

            const newDiv = `<div style="font-family: '${fontName}'; margin-bottom: 10px;">
                ${fontName}
            </div>`;
            const font_details = `<tr class="uploadedFonts" id="uploadedFontsTr">\
                                    <td>${fontName}</td>\
                                    <td style="font-family: '${fontName}'; margin-bottom: 10px;">${fontName}</td>\
                                    <td id="${fontName}" class="removeLoadedFont">Delete</td>\
                                </tr>`;

            // Append the new div to the font container
            $('#fontHolder').after( font_details );
        });

        if( loadedFonts.length > 0 ){
            $("#emptyFontLoad").empty();
            $('#fontRows').empty();
            loadedFonts.forEach(function(fontName) {
                options += `<option value="${fontName}">${fontName}</option>`;
            });
            for( let i = 0; i < 2; i++ ) {
                let getStyle = loadSelectFonts( options );
                $('#fontRows').append( getStyle );
            }
        }else{
            $("#emptyFontLoad").append( '<span>You Do Not Have Any Uploaded Font Files, Please Upload Font Files For Creating Groups</span>' );
            // alert( 'You Do Not Have Any Uploaded Font Files, Please Upload Font Files For Creating Groups');
        }


    }
    const fetchFonts = async (url) => {
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
        let clickedId = $(this).attr('id');
        let fileName = clickedId+'.ttf';

        try {
            const response = await $.ajax({
                url: 'API/remove_file.php', // PHP script to handle deletion
                type: 'POST',
                data: {filename: fileName},
            });
            $(this).parent().hide();
            $(this).parent().remove();
        } catch (error) {
            $('#uploadStatus').html('<div class="alert alert-danger">Failed to delete the file. Please try again.</div>');
        }
    });


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
            alert('At Least Tow Fonts Required');
        }
    });


    function display_loaded_groups( data ) {
        $('#loadGroups').empty();

        $.each(data, function(index, item) {
            var row = '<tr id="' + item.key + '">\
                            <td>' + item.name + '</td>\
                            <td>' + item.font_name + '</td>\
                            <td>' + item.counts + '</td>\
                            <td><span class="editFontGroup" id="edit-'+item.key+'">Edit</span> <span class="deleteFontGroup" id="delete-'+item.key+'">Delete</span></td>\
                        </tr>';

            $('#loadGroups').append(row);
        });
    }
    function display_single_font_group( data ) {
        // console.log( data );
        var singleFontRow = '<tr id="' + data.key + '">\
                        <td>' + data.name + '</td>\
                        <td>' + data.font_name + '</td>\
                        <td>' + data.counts + '</td>\
                        <td><span class="editFontGroup" id="edit-'+data.key+'">Edit</span> <span class="deleteFontGroup" id="delete-'+data.key+'">Delete</span></td>\
                    </tr>';

        $('#loadGroups').append( singleFontRow );
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
        $('#validationMessage').html('<div class="alert alert-success">Form is validated and submitting to store.</div>');

        $.ajax({
            url: 'API/create_group.php',
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                let result= JSON.parse(response);
                if ( result.status === 'success') {
                    $('#validationMessage').html('<div class="alert alert-success">Group created successfully.</div>');


                    setTimeout( function() {
                        $('#fontGroupForm')[0].reset();
                        $('#validationMessage').empty();
                    }, 1000);


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

    function removeElement( string ) {

        return string.split('-')[1];

    }

    const editPopup = ( key ) =>{

        $.ajax({
            url: 'API/get_font_group_data.php',
            type: 'POST',
            data: {key : key,},
            success: function(response) {
                let result= JSON.parse(response);
                if( result.status ){

                    let group_data = result.data.group_data;
                    let font_group_details = group_data[0]['font_details'];
                    let fontFiles = result.data.font_files;
                    let details = '';

                    font_group_details.forEach( function( group_detail ){
                        let fontOptions = '' ;
                        fontFiles.forEach(function( fontName ) {
                            if( group_detail['font_name'] === fontName ){
                                var isSelected = 'selected';
                            }else{
                                isSelected = '';
                            }
                            fontOptions += `<option value="${fontName}" ${isSelected}>${fontName}</option>`;
                        });

                        details += `<div class="fontDetailsHolder" id="removeFromEdit-${group_detail['id']}">
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="${group_detail['id']}[title]" required placeholder="Font name" value="${group_detail.title}">
                                        </div>
                                        <div class="form-group">
                                            <select class="form-control" name="${group_detail['id']}[font_name]" required>
                                                <!--<option value=fontName">fontName</option>-->
                                                ${fontOptions}
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <input type="number" class="form-control" name="${group_detail['id']}[size]" min="0" required placeholder="1.0" value="${group_detail.size}">
                                        </div>
                                        <div class="form-group">
                                            <input type="number" class="form-control" name="${group_detail['id']}[price]" min="0" required placeholder="0" value="${group_detail.price}">
                                        </div>
                                        <div class="removeEditRows" >X</div>
                                    </div>`;
                    });

                    let editPopupDisplay = ` <div id="overlay" class="position-fixed w-100 h-100 bg-dark" style="display: block; top: 0; left: 0; opacity: 0.5; z-index: 1040;"></div>
                            <div id="edit-popup" class="position-fixed p-3 shadow rounded bg-light text-center" style="display: block; width: 650px; z-index: 1050;">
                                <button type="button" id="close-popup" class="close text-dark" aria-label="Close" style="padding-left: 5px;">
                                    <i class="fas fa-times"></i>
                                </button>
                                <h2 id="popup-message" class="mb-3">Edit Font Group</h2>
                                <form id="editFontGroupform" >
                                    <div class="form-group mr-2">
                                        <input type="text" class="form-control " name="titleName[${group_data[0]['key']}]" required placeholder="Group title" value="${group_data[0]['name']}">
                                    </div>
                                    <div class="font-row" id="editFontRow">
                                        ${details}
                                    </div>
                                    <input class=" btn btn-success btn-sm" type="submit" id="submitEditForm-${group_data[0]['key']}" value="Submit">
                                </form>
                            </div>`;

                    $('body').append( editPopupDisplay );
                }


            },
            error: function() {
                alert( 'Failed To Delete' );
            }
        });

    }


    $(document).on( 'submit', '#editFontGroupform', function( event ){
        let submitButtonId = $('#editFontGroupform input[type="submit"]').attr('id');
        let groupKey = removeElement( submitButtonId );

        event.preventDefault();
        $.ajax({
            url: 'API/edit_group.php',
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                let result= JSON.parse(response);
                // console.log( result.data );
                if( result.status ){
                    $("#"+groupKey).remove();
                    display_single_font_group( result.data );
                    hidePopup();

                    make_popup( result.message, '' );
                    $("#buttonHolder").empty();
                    setTimeout( function(){
                        hidePopup();
                    }, 1000);
                    // alert( result.message );
                }

            },
            error: function() {
                // Handle error
                $('#validationMessage').html('<div class="alert alert-danger">Failed to create group. Please try again.</div>');
            }
        });
    });

    $(document).on( 'click', '.removeEditRows', function(){
        let childCount = $('#editFontRow').children('.fontDetailsHolder').length;
        if( childCount > 2 ){
            $(this).parent().remove();
        }else{
            alert( 'You need At Least Two Fonts ' );
        }

    });

    $(document).on( 'click', '.editFontGroup', function(){
        let clickedId = $(this).attr('id');
        let key = removeElement(clickedId).trim();
        editPopup( key );
    });


    function hidePopup() {

        $('#delete-popup, #edit-popup, #overlay').empty();
        $('#delete-popup, #edit-popup, #overlay').remove();

    }

    $(document).on( 'click', '#close-popup, #confirm-no', function(){
        hidePopup();
    });

    const remove_items = ( key )=> {

        $.ajax({
            url: 'API/delete_group.php',
            type: 'POST',
            data: {key : key,},
            success: function(response) {
                let result= JSON.parse(response);
                if ( result.status ) {
                    hidePopup();
                    $( "#"+key ).remove();
                }else{
                    console.error('Error:', response.message);
                }
            },
            error: function() {
                alert( 'Failed To Delete' );
            }
        });
    }

    $(document).on( 'click', '.confirm-yes', function(){

        remove_btn_from_popup( "Font Group is Deleting..." );
        let clickedId = $(this).attr('id');
        let key = removeElement(clickedId).trim();
        remove_items( key );

    });

    const remove_btn_from_popup = ( message ) =>{
        $("#buttonHolder").hide();
        $("#popup-message").empty();
        $("#popup-message").text( message );
    }
    function make_popup( message, key ){
        let confirmationPopup = ` <div id="overlay" class="position-fixed w-100 h-100 bg-dark" style="display: block; top: 0; left: 0; opacity: 0.5; z-index: 1040;"></div>
                                  <div id="delete-popup" class="position-fixed p-3 shadow rounded bg-light text-center" style="display: block; width: 350px; z-index: 1050;">
                                        <button type="button" id="close-popup" class="close text-dark" aria-label="Close" style="padding-left: 5px;">
                                            <i class="fas fa-times"></i>
                                        </button>
                                        <p id="popup-message" class="mb-3">${message}</p>
                                        <div id="buttonHolder">
                                            <button class="confirm-yes btn btn-success btn-sm" id="confirm-${key}">Yes</button>
                                            <button id="confirm-no" class="btn btn-secondary btn-sm">No</button>
                                        </div>
                                    </div>`;

        $('body').append( confirmationPopup );
    }

    $(document).on( 'click', '.deleteFontGroup', function(){

        let clickedId = $(this).attr('id');
        let key = removeElement(clickedId).trim();
        make_popup( 'Are you sure you want to delete this item?', key );

    });



});
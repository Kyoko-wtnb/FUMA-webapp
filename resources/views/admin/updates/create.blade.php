@extends('layouts.master')

@section('stylesheets')
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" />

    <style>
        .options {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 15px;
        }

        button {
            height: 28px;
            width: 28px;
            display: grid;
            place-items: center;
            border-radius: 3px;
            border: none;
            background-color: #ffffff;
            outline: none;
            color: #020929;
        }

        select {
            padding: 7px;
            border: 1px solid #020929;
            border-radius: 3px;
        }

        .options label,
        .options select {
            font-family: "Poppins", sans-serif;
        }

        .input-wrapper {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        input[type="color"] {
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            background-color: transparent;
            width: 40px;
            height: 28px;
            border: none;
            cursor: pointer;
        }

        input[type="color"]::-webkit-color-swatch {
            border-radius: 15px;
            box-shadow: 0 0 0 2px #ffffff, 0 0 0 3px #020929;
        }

        input[type="color"]::-moz-color-swatch {
            border-radius: 15px;
            box-shadow: 0 0 0 2px #ffffff, 0 0 0 3px #020929;
        }

        #text-input {
            margin-top: 1px;
            border: 1px solid #dddddd;
            padding: 20px;
            height: 50vh;
            overflow: auto;
        }

        .active {
            background-color: #e0e9ff;
        }
    </style>
@endsection

@section('content')
    <div class="container" style="padding-top: 50px;">
        <div class="table-title">
            <div class="row">
                <div class="col-sm-10">
                    <h2>Create New Update</h2>
                </div>
            </div>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="container">
            <div class="options">
                <!-- Text Format -->
                <button id="bold" class="option-button format">
                    <i class="fa-solid fa-bold"></i>
                </button>
                <button id="italic" class="option-button format">
                    <i class="fa-solid fa-italic"></i>
                </button>
                <button id="underline" class="option-button format">
                    <i class="fa-solid fa-underline"></i>
                </button>
                <button id="strikethrough" class="option-button format">
                    <i class="fa-solid fa-strikethrough"></i>
                </button>
                <button id="superscript" class="option-button script">
                    <i class="fa-solid fa-superscript"></i>
                </button>
                <button id="subscript" class="option-button script">
                    <i class="fa-solid fa-subscript"></i>
                </button>
                <!-- List -->
                <button id="insertOrderedList" class="option-button">
                    <div class="fa-solid fa-list-ol"></div>
                </button>
                <button id="insertUnorderedList" class="option-button">
                    <i class="fa-solid fa-list"></i>
                </button>
                <!-- Undo/Redo -->
                <button id="undo" class="option-button">
                    <i class="fa-solid fa-rotate-left"></i>
                </button>
                <button id="redo" class="option-button">
                    <i class="fa-solid fa-rotate-right"></i>
                </button>
                <!-- Link -->
                <button id="createLink" class="adv-option-button">
                    <i class="fa fa-link"></i>
                </button>
                <button id="unlink" class="option-button">
                    <i class="fa fa-unlink"></i>
                </button>
                <!-- Alignment -->
                <button id="justifyLeft" class="option-button align">
                    <i class="fa-solid fa-align-left"></i>
                </button>
                <button id="justifyCenter" class="option-button align">
                    <i class="fa-solid fa-align-center"></i>
                </button>
                <button id="justifyRight" class="option-button align">
                    <i class="fa-solid fa-align-right"></i>
                </button>
                <button id="justifyFull" class="option-button align">
                    <i class="fa-solid fa-align-justify"></i>
                </button>
                <button id="indent" class="option-button spacing">
                    <i class="fa-solid fa-indent"></i>
                </button>
                <button id="outdent" class="option-button spacing">
                    <i class="fa-solid fa-outdent"></i>
                </button>
                <!-- Headings -->
                <select id="formatBlock" class="adv-option-button">
                    <option value="H1">H1</option>
                    <option value="H2">H2</option>
                    <option value="H3">H3</option>
                    <option value="H4">H4</option>
                    <option value="H5">H5</option>
                    <option value="H6">H6</option>
                </select>
                <!-- Font -->
                <select id="fontName" class="adv-option-button"></select>
                <select id="fontSize" class="adv-option-button"></select>
                <!-- Color -->
                <div class="input-wrapper">
                    <input type="color" id="foreColor" class="adv-option-button" />
                    <label for="foreColor">Font Color</label>
                </div>
                <div class="input-wrapper">
                    <input type="color" id="backColor" class="adv-option-button" />
                    <label for="backColor">Highlight Color</label>
                </div>
            </div>
            {{ html()->form('POST', url('admin/updates'))->open() }}
            <div style="text-align:right;">
                <label for="title">Title:</label>
                <input type="text" id="title" name="title">

                <label for="ver">Version:</label>
                <input type="text" id="ver" name="version">

                <label for="writer">Writer:</label>
                <input type="text" id="writer" name="writer">

            </div><br>

            <div style="text-align:right;">
                <label for="visible">Visible:</label>
                <input type="checkbox" id="visible" name="is_visible" checked />
            </div>

            <label>Enter descrition here:</label>
            <div id="text-input" contenteditable="true" name="text"></div><br>
            <input type="hidden" id="hiddeninput" name="description" />

            <input class="btn btn-info" id="save" type="submit" value="Save" name="submit" />
            {{ html()->form()->close() }}
        </div>
    </div>
@endsection

@section('scripts')
    {{-- Imports from the web --}}

    {{-- Hand written ones --}}
    <script>
        let optionsButtons = document.querySelectorAll(".option-button");
        let advancedOptionButton = document.querySelectorAll(".adv-option-button");
        let fontName = document.getElementById("fontName");
        let fontSizeRef = document.getElementById("fontSize");
        let writingArea = document.getElementById("text-input");
        let linkButton = document.getElementById("createLink");
        let alignButtons = document.querySelectorAll(".align");
        let spacingButtons = document.querySelectorAll(".spacing");
        let formatButtons = document.querySelectorAll(".format");
        let scriptButtons = document.querySelectorAll(".script");

        //List of fontlist
        let fontList = [
            "Arial",
            "Verdana",
            "Times New Roman",
            "Garamond",
            "Georgia",
            "Courier New",
            "cursive",
        ];

        //Initial Settings
        const initializer = () => {
            //function calls for highlighting buttons
            //No highlights for link, unlink,lists, undo,redo since they are one time operations
            highlighter(alignButtons, true);
            highlighter(spacingButtons, true);
            highlighter(formatButtons, false);
            highlighter(scriptButtons, true);

            //create options for font names
            fontList.map((value) => {
                let option = document.createElement("option");
                option.value = value;
                option.innerHTML = value;
                fontName.appendChild(option);
            });

            //fontSize allows only till 7
            for (let i = 1; i <= 7; i++) {
                let option = document.createElement("option");
                option.value = i;
                option.innerHTML = i;
                fontSizeRef.appendChild(option);
            }

            //default size
            fontSizeRef.value = 3;
        };

        //main logic
        const modifyText = (command, defaultUi, value) => {
            //execCommand executes command on selected text
            document.execCommand(command, defaultUi, value);
        };

        //For basic operations which don't need value parameter
        optionsButtons.forEach((button) => {
            button.addEventListener("click", () => {
                modifyText(button.id, false, null);
            });
        });

        //options that require value parameter (e.g colors, fonts)
        advancedOptionButton.forEach((button) => {
            button.addEventListener("change", () => {
                modifyText(button.id, false, button.value);
            });
        });

        //link
        linkButton.addEventListener("click", () => {
            let userLink = prompt("Enter a URL");
            //if link has http then pass directly else add https
            if (/http/i.test(userLink)) {
                modifyText(linkButton.id, false, userLink);
            } else {
                userLink = "http://" + userLink;
                modifyText(linkButton.id, false, userLink);
            }
        });

        //Highlight clicked button
        const highlighter = (className, needsRemoval) => {
            className.forEach((button) => {
                button.addEventListener("click", () => {
                    //needsRemoval = true means only one button should be highlight and other would be normal
                    if (needsRemoval) {
                        let alreadyActive = false;

                        //If currently clicked button is already active
                        if (button.classList.contains("active")) {
                            alreadyActive = true;
                        }

                        //Remove highlight from other buttons
                        highlighterRemover(className);
                        if (!alreadyActive) {
                            //highlight clicked button
                            button.classList.add("active");
                        }
                    } else {
                        //if other buttons can be highlighted
                        button.classList.toggle("active");
                    }
                });
            });
        };

        const highlighterRemover = (className) => {
            className.forEach((button) => {
                button.classList.remove("active");
            });
        };

        window.onload = initializer();
    </script>

    <script>
        $(function() {
            $('#save').click(function() {
                var mysave = $('#text-input').html();
                $('#hiddeninput').val(mysave);
            });
        });
    </script>

    {{-- Imports from the project --}}
@endsection

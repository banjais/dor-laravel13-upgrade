<style>
/* Shared compact styling for Chatbot admin pages */
#main-content.chatbot-admin-compact {
    padding-top: 8px;
}

#main-content.chatbot-admin-compact h3 {
    margin: 5px 0 8px;
    font-size: 20px;
    line-height: 1.3;
}

#main-content.chatbot-admin-compact h4 {
    margin: 14px 0 7px;
    font-size: 16px;
    line-height: 1.4;
}

#main-content.chatbot-admin-compact p {
    margin: 0 0 9px;
}

#main-content.chatbot-admin-compact form {
    margin-bottom: 10px;
}

#main-content.chatbot-admin-compact .form-group,
#main-content.chatbot-admin-compact .control-group {
    margin-bottom: 9px;
}

#main-content.chatbot-admin-compact label {
    margin-bottom: 4px;
    line-height: 1.4;
}

#main-content.chatbot-admin-compact input.form-control,
#main-content.chatbot-admin-compact select.form-control,
#main-content.chatbot-admin-compact textarea.form-control,
#main-content.chatbot-admin-compact .form-control {
    min-height: 32px;
    height: 32px;
    padding: 5px 9px;
    font-size: 13px;
    line-height: 1.4;
    box-sizing: border-box;
}

#main-content.chatbot-admin-compact textarea.form-control {
    height: auto;
    min-height: 64px;
}

#main-content.chatbot-admin-compact input[type="checkbox"],
#main-content.chatbot-admin-compact input[type="radio"] {
    min-height: auto;
    height: auto;
    margin-right: 5px;
}

#main-content.chatbot-admin-compact .btn {
    padding: 6px 12px;
    font-size: 13px;
    line-height: 1.35;
}

#main-content.chatbot-admin-compact .card-body {
    padding: 12px;
}

#main-content.chatbot-admin-compact .card {
    margin-bottom: 12px;
}

#main-content.chatbot-admin-compact table th,
#main-content.chatbot-admin-compact table td {
    padding: 7px 9px;
    vertical-align: middle;
}

#main-content.chatbot-admin-compact .row-fluid,
#main-content.chatbot-admin-compact .row {
    margin-bottom: 6px;
}

/* General Settings: consistent single-column form */
#main-content.chatbot-admin-compact .chatbot-general-form {
    width: 100%;
    max-width: 100%;
    margin: 0;
    padding: 0;
}

#main-content.chatbot-admin-compact .chatbot-general-form .control-group {
    display: block !important;
    float: none !important;
    width: 100% !important;
    margin: 0 0 13px !important;
    padding: 0 !important;
    overflow: visible;
}

#main-content.chatbot-admin-compact .chatbot-general-form .control-label {
    display: block !important;
    float: none !important;
    width: auto !important;
    margin: 0 0 5px !important;
    padding: 0 !important;
    text-align: left !important;
    font-weight: 600;
    line-height: 1.4;
}

#main-content.chatbot-admin-compact .chatbot-general-form .controls {
    display: block !important;
    float: none !important;
    width: 100% !important;
    min-height: 0;
    margin: 0 !important;
    padding: 0 !important;
}

#main-content.chatbot-admin-compact .chatbot-general-form .controls > input:not([type="checkbox"]):not([type="hidden"]):not([type="color"]),
#main-content.chatbot-admin-compact .chatbot-general-form .controls > textarea {
    display: block;
    float: none !important;
    width: 100% !important;
    max-width: 560px;
    margin: 0 !important;
    box-sizing: border-box;
}

#main-content.chatbot-admin-compact .chatbot-general-form .controls > textarea {
    max-width: 720px;
    min-height: 76px;
    height: auto;
}

#main-content.chatbot-admin-compact .chatbot-general-form .controls > select {
    display: block;
    float: none !important;
    width: 100%;
    max-width: 300px;
    height: 34px;
    margin: 0 !important;
    box-sizing: border-box;
}

#main-content.chatbot-admin-compact .chatbot-general-form .controls > input[type="number"] {
    max-width: 180px;
}

#main-content.chatbot-admin-compact .chatbot-general-form input[type="color"] {
    display: block;
    width: 54px;
    height: 32px;
    padding: 2px;
    margin: 0;
    box-sizing: border-box;
}

#main-content.chatbot-admin-compact .chatbot-general-form .controls > label {
    display: inline-flex !important;
    align-items: center;
    width: auto !important;
    float: none !important;
    margin: 2px 0 !important;
    padding: 0 !important;
    font-weight: normal;
    line-height: 1.5;
}

#main-content.chatbot-admin-compact .chatbot-general-form input[type="checkbox"] {
    display: inline-block;
    width: auto;
    height: auto;
    min-height: 0;
    margin: 0 7px 0 0 !important;
    vertical-align: middle;
}

#main-content.chatbot-admin-compact .chatbot-general-form .help-block,
#main-content.chatbot-admin-compact .chatbot-general-form .help-inline {
    display: block;
    margin: 5px 0 0 !important;
    padding: 0 !important;
    line-height: 1.45;
    color: #777;
}

#main-content.chatbot-admin-compact .chatbot-general-form h4 {
    margin-top: 17px;
    margin-bottom: 7px;
}

#main-content.chatbot-admin-compact .chatbot-general-form hr {
    margin: 7px 0 12px;
}

/* Save / Restore actions */
#main-content.chatbot-admin-compact .chatbot-general-form .form-actions {
    display: flex !important;
    flex-wrap: wrap;
    align-items: center;
    gap: 10px;
    width: 100%;
    margin: 14px 0 0 !important;
    padding: 12px 0 0 !important;
    border-top: 1px solid #e5e5e5;
    background: transparent;
    box-sizing: border-box;
}

#main-content.chatbot-admin-compact .chatbot-general-form .form-actions > span {
    flex: 1 1 220px;
    margin: 0 !important;
}

#main-content.chatbot-admin-compact .chatbot-general-form .form-actions button {
    flex: 0 0 auto;
}

/* Other Chatbot tables and mobile layout */
@media (max-width: 767px) {
    #main-content.chatbot-admin-compact {
        padding-left: 8px;
        padding-right: 8px;
    }

    #main-content.chatbot-admin-compact .chatbot-general-form .controls > input:not([type="checkbox"]):not([type="hidden"]):not([type="color"]),
    #main-content.chatbot-admin-compact .chatbot-general-form .controls > textarea,
    #main-content.chatbot-admin-compact .chatbot-general-form .controls > select {
        max-width: 100%;
    }

    #main-content.chatbot-admin-compact .chatbot-general-form .form-actions {
        align-items: stretch;
    }

    #main-content.chatbot-admin-compact .chatbot-general-form .form-actions > span {
        flex-basis: 100%;
        order: 3;
    }
}

/* Keep section heading and first setting closely aligned */
#main-content.chatbot-admin-compact .chatbot-general-form h4 {
    margin: 10px 0 6px !important;
    padding: 0 !important;
}

#main-content.chatbot-admin-compact .chatbot-general-form h4 + hr {
    margin: 0 0 9px !important;
}

#main-content.chatbot-admin-compact .chatbot-general-form .control-group:first-of-type {
    margin-top: 0 !important;
}

/* Checkbox description stays beside its checkbox */
#main-content.chatbot-admin-compact .chatbot-general-form .controls > label {
    display: inline-flex !important;
    align-items: center;
    gap: 7px;
    margin: 0 !important;
}


/* Highlight Chatbot section headings */
#main-content.chatbot-admin-compact .chatbot-general-form h4 {
    display: block;
    background: #eef2f6;
    color: #263746;
    border-left: 4px solid #1769aa;
    border-radius: 3px;
    padding: 9px 12px !important;
    margin: 18px 0 12px !important;
    font-size: 16px;
    font-weight: 700;
    line-height: 1.4;
}

#main-content.chatbot-admin-compact .chatbot-general-form h4 + hr {
    display: none;
}

#main-content.chatbot-admin-compact .chatbot-general-form h4 + p {
    margin-top: 0;
}

</style>

<?php
/** @global $params */
?>
<html>
    <head>
        <title>Example 1 widget</title>
        <style>
            form {
                padding: 10px;
            }
            form ul {
                list-style: none;
                padding: 0;
            }
        </style>
    </head>
    <body>
        <h1>Example 1 widget</h1>
        <form>
            <ul>
                <li>
                    Choose field <span id="listExampleField"></span>
                </li>
            </ul>

        </form>
    <pre>
        <h2>Debug</h2>
        <?=var_export($params)?>
    </pre>
    <script src="https://js.amo.tm/v1/sdk.js"></script>
    <script>
        const amoSDK = window.AmoSDK();
        const elements = amoSDK.elements();
        const listExampleField = elements.create('inline-select', {
            // className: 'someClassName',
            items: [{id: '', option: '...'}],
            name: 'list_example',
            value: '<?=$params['input_values']['list_example_selected_value'] ?? ''?>',
            onChange: function (value) {
                amoSDK.setInputValues({
                    list_example_selected_value: value,
                })
            },
        });
        listExampleField.mount('#listExampleField')
        amoSDK.request('rpa-fields').then((fields) => {
            const fieldOptions = [{ id: '', option: '...' }].concat(Object.values(fields).map((field) => ({
                id: field.id,
                option: field.title
            })));
            listExampleField.update({items: fieldOptions})
        });
        amoSDK.on('update:rpa-fields', (fields) => {
            const fieldOptions = [{ id: '', option: '...' }].concat(Object.values(fields).map((field) => ({
                id: field.id,
                option: field.title
            })));
            listExampleField.update({items: fieldOptions});
        });
    </script>
    </body>
</html>

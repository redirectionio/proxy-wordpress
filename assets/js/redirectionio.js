/*
 * Add a new connection dynamically.
 *
 * @param {event} e
 */
function rioAddConnection(e)
{
    e.preventDefault();

    // get DOM elements
    var form = document.getElementById('rio_connections');
    var tables = form.getElementsByTagName('table');
    var lastTable = tables[tables.length - 1];
    var inputs = lastTable.getElementsByTagName('input');
    var lastInput = inputs[inputs.length - 1];
    var h2Arr = form.getElementsByTagName('h2');
    var h2Title = h2Arr[0].innerHTML.split('#')[0];
    var lastH2 = h2Arr[h2Arr.length - 1];
    var titleId = parseInt(lastH2.innerHTML.split('#')[1]);

    // find next id
    var id = parseInt(lastInput.name.split(/\[|\]/)[3]);
    var newId = id + 1;

    // clone a table of inputs (name, host and port)
    var clone = tables[0].cloneNode(true);
    var cloneInputs = clone.getElementsByTagName('input');

    // update id/name for each new input field
    [].forEach.call(cloneInputs, function(input) {
        rioUpdateInput(input, newId);
    });

    var newH2 = rioCreateH2(h2Title, ++titleId);

    // insert h2 and new fields to DOM
    var addButton = document.getElementById('rio_connections_add');
    form.insertBefore(newH2, addButton);
    form.insertBefore(clone, addButton);

    // add a remove button to first h2 element
    if (h2Arr[0].firstElementChild === null) {
        var newFirstH2 = rioCreateH2(h2Title, 1);
        form.replaceChild(newFirstH2, h2Arr[0]);
    }
}

/*
 * Remove a connection dynamically.
 *
 * @param {event} e
 */
function rioRemoveConnection(e)
{
    e.preventDefault();

    if (true !== confirm(confirmStr)) {
        return;
    }

    var title = e.target.parentElement;
    var status = rioNextWithClass(title, 'rio_connection_status');
    var fields = rioNextWithClass(title, 'form-table');
    var titleId = parseInt(title.innerHTML.split('#')[1]);
    var form = document.getElementById('rio_connections');
    var h2Arr = form.getElementsByTagName('h2');
    var h2Title = h2Arr[0].innerHTML.split('#')[0];

    [].forEach.call(h2Arr, function(h2) {
        var id = parseInt(h2.innerHTML.split('#')[1]);

        if (id > titleId) {
            var inputs = rioNextWithClass(h2, 'form-table').getElementsByTagName('input');

            [].forEach.call(inputs, function(input) {
                var inputId = parseInt(input.id.split('_')[1]);
                rioUpdateInput(input, --inputId, true);
            });

            var newH2 = rioCreateH2(h2Title, --id);
            form.replaceChild(newH2, h2);
        }
    });

    // remove connection
    fields.remove();
    title.remove();

    if (status !== null) {
        status.remove();
    }

    if (1 === h2Arr.length) {
        h2Arr[0].removeChild(h2Arr[0].firstElementChild);
    }
}

/*
 * Update input `name` and `id` properties with a new `id`.
 *
 * Example :
 * with id = 5 / keepValue = false
 * from <input id="rio_12_type" name="rio[connections][12][type]" value="example" ... />
 * to <input id="rio_5_type" name="rio[connections][5][type]" value="" ... />
 *
 * @param {object} input An HTML input
 * @param {int} id New input id
 * @param {bool} keepValue If false, we wipe the input value
 */
function rioUpdateInput(input, id, keepValue)
{
    keepValue = typeof keepValue !== 'undefined' ? keepValue : false;

    var inputNameSplit = input.name.split(/\[|\]/);
    var inputIdSplit = input.id.split('_');
    var newName = '';
    var newIdName = '';
    var i;

    for (i = 0; i < inputIdSplit.length; ++i) {
        switch (i) {
            case 0:
                newIdName += inputIdSplit[i];
                break;
            case 1:
                newIdName += '_' + id;
                break;
            default:
                newIdName += '_' + inputIdSplit[i];
        }
    }

    for (i = 0; i < inputNameSplit.length; ++i) {
        switch (i) {
            case 0:
                newName += inputNameSplit[i];
                break;
            case 1:
                newName += '[connections]';
                break;
            case 2:
                // do nothing
                break;
            case 3:
                newName += '[' + id;
                break;
            default:
                newName += ((0 === i%2) ? ']' : '[') + inputNameSplit[i];
        }
    }

    input.name = newName;
    input.id = newIdName;

    if (keepValue === false) {
        input.value = '';
    }
}

/*
 * Create an HTML h2 element containing "title #id".
 *
 * @param {string} title H2 title value
 * @param {int} id
 * @return {object} H2 element created
 */
function rioCreateH2(title, id)
{
    var h2 = document.createElement('h2');
    var removeButton = document.createElement('span');
    removeButton.className = 'dashicons dashicons-trash rio_connections_remove';
    removeButton.onclick = function(e) {
        rioRemoveConnection(e);
    };
    h2.innerHTML = title + '#' + id + ' ';
    h2.appendChild(removeButton);

    return h2;
}

/*
 * Check if an HTML element `el` has class `className`.
 *
 * @param {object} el HTML element
 * @param {string} className Class name to check
 * @return {bool}
 */
function rioHasClass(el, className) {
    var str = " " + el.className + " ";
    var testClassName = " " + className + " ";
    return(str.indexOf(testClassName) != -1) ;
}

/*
 * Find nextSibling element of node `node` with className `className`.
 *
 * @param {object} node A DOM node
 * @param {string} className Class name to find
 * @return {mixed} node element if found else null
 */
function rioNextWithClass(node, className) {
    while (node = node.nextSibling) {
        if (rioHasClass(node, className)) {
            return node;
        }
    }
    return null;
}

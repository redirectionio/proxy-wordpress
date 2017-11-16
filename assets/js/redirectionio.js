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

            var inputs = h2.nextSibling.getElementsByTagName('input');

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

function rioUpdateInput(input, id, keepValue)
{   
    keepValue = typeof keepValue !== 'undefined' ? keepValue : false;

    var inputNameSplit = input.name.split(/\[|\]/);
    var inputIdSplit = input.id.split('_');
    var newName = '';
    var newIdName = '';

    for (var i = 0; i < inputIdSplit.length; ++i) {
        if (0 === i) {
            newIdName += inputIdSplit[i];
        } else if (1 === i) {
            newIdName += '_' + id;
        } else {
            newIdName += '_' + inputIdSplit[i];
        }
    }

    for (var i = 0; i < inputNameSplit.length; ++i) {
        if (0 === i) {
            newName += inputNameSplit[i];
        } else if (1 === i) {
            newName += '[connections]';
        } else if (2 === i) {
            continue;
        } else if (3 === i) {
            newName += '[' + id;
        } else if (0 === i%2) {
            newName += ']' + inputNameSplit[i];
        } else {
            newName += '[' + inputNameSplit[i];
        }
    }

    input.name = newName;
    input.id = newIdName;

    if (keepValue === false) {
        input.value = '';
    }
}

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

function rioHasClass(el, className) {
    var str = " " + el.className + " ";
    var testClassName = " " + className + " ";
    return(str.indexOf(testClassName) != -1) ;
}

function rioNextWithClass(node, className) {
    while (node = node.nextSibling) {
        if (rioHasClass(node, className)) {
            return node;
        }
    }
    return null;
}

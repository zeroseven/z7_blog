const id = document.getElementById('tagify_id').value;
const whitelist = JSON.parse(document.getElementById('tagify_whitelist').value);
console.log(id);console.log(whitelist);
new Tagify(document.getElementById(id), {
    whitelist: whitelist,
    originalInputValueFormat: (function (valuesArr) {
        return valuesArr.map(function (item) {
            return item.value;
        }).join(", ").trim();
    })
})

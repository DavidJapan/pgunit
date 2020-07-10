ko.dt.SelectedItems = function (dataTableFactory, items) {
    var self = this, primaryKey = dataTableFactory.primaryKey.array();
    self.items = items;
    self.getKeys = function(){
        var item, pk, pkValues = [], field, keys = [];
        for(var i = 0; i < self.items.length; i += 1){
            item = self.items[i];
            pkValues = [];
            for(pk = 0; pk < primaryKey.length; pk += 1){
                field = primaryKey[pk];
                pkValues[pk] = self.items[i][field]();
            }
            keys.push(pkValues);
        } 
        return keys;
    };
    self.getKeyValuePairs = function(){
        var item, pk, pkValues = [], field, keyValuePairs = [];
        for(var i = 0; i < self.items.length; i += 1){
            item = self.items[i];
            pkValues = {};
            for(pk = 0; pk < primaryKey.length; pk += 1){
                field = primaryKey[pk]();
                pkValues[field] = self.items[i][field]();
            }
            keyValuePairs.push(pkValues);
        }
        return keyValuePairs;        
    };
};
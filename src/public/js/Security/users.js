(function(){
    if($("[data-button-type=addRolesToUserS]") !== undefined){
        $("[data-button-type=addRolesToUserS]").unbind('click');
    }
    viewListPers =function(button,Title,URL){
        var dataEntry=$(button).data('entry');
        var modelKey=$(button).data('model-key');
        var type='all';
        var ModalId=$(button).data('button-type');
        var addLink=$(button).attr('data-button-addLink');
        createModal(button,ModalId,Title);
        sent={};
        sent['type']=type;
        sent[modelKey]=dataEntry
        sendSecRequestToGetAll('all',dataEntry,URL,sent,ModalId,'name',null,addLink,modelKey);
    }
    createModal=function(button,id,title){
        titleId=$(button).data('title');
        fst=$(button).parent().parent().children()[titleId];
        fst=$(fst).children()[0];
        fst=$(fst).html();
        fst=fst.toString().replace(/^\s+|\s+$/g, "");
        var myModal=$(`<div class="modal fade" id="` + id + `Modal" tabindex="-1" aria-labelledby="`+id+`ModalLabel" data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-fullscreen-xxl-down">
            <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="`+id+`ModalLabel">`+title+` : `+fst+`</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
            </div>
        </div>
        </div>`);
        //lunch show modal
        if($(`#`+id+`Modal`).length == 0){$('body').append(myModal);}
        var myModalEl = document.querySelector(`#`+id+`Modal`)
        var modal = bootstrap.Modal.getOrCreateInstance(myModalEl) // Returns a Bootstrap modal instance
        modal.show()
    }
    sendSecRequestToGetAll=function (Type,dataEntry,URL,DATA,DivId,fstText,ndText=null,addLink,modelKey){
        var ResultFn="plusminus";
        var ndText=ndText;
        jQuery.ajax({
            url:URL,
            language:window.Amer.Language,
            dir:window.Amer.dir,
            dataType:'json',
            crossDomain:true,
            contentType:"application/x-www-form-urlencoded; charset=UTF-8",
            data:DATA,
            type:'post',
        }).done(function(data){
            var allData=data[1];
            var selectedDataIds=data[0]
            var divFst=$(`<div class="row"></div>`);
            var col=$(`<div class="mb-3"><label class="form-label" for="search">بحث</label></div>`);
            var SearchInput=$(`<input type="text" id="filterlist" class="form-control search" name="search" placeholder="Name">`);
            col.append(SearchInput);
            divFst.append(col)
            var divFstListGroup=$(`<ul class="list-group list-group-flush"></ul>`);
            $.each(allData,function(k,v){
                var DataId=v['id'];
                if(in_array(selectedDataIds,DataId)){var plus='minus';}else{var plus='plus';}
                if(ndText == null){var Second='';}else{var Second=v[ndText];}
                var fstDivInnerText=`<li class="list-group-item d-flex justify-content-between align-items-start">
                                        <div class="ms-2 me-auto">
                                            <div class="fw-bold">`+v[fstText]+`</div>
                                            `+Second+`
                                        </div>
                                        <span class="badge bg-primary rounded-circle" onclick="${ResultFn}('${dataEntry}','${v['id']}','${plus}','${addLink}','${modelKey}')">
                                            <i class="fa fa-`+plus+`" data-int="`+v['id']+`"></i>
                                        </span>
                                    </li>`;
                                    
                var fstDivInner=$(fstDivInnerText);
                divFstListGroup.append(fstDivInner);
            });
            divFst.append(divFstListGroup);
            $('#'+DivId+'Modal .modal-dialog .modal-content .modal-body').html(divFst);
            $(SearchInput).on('input',function(e){searchModal(e.target);})
        });
    }
    plusminus=(dataEntry,id,action,addLink,modelKey)=>{
        if(action == 'minus'){
            action='plus';
        }else{
            action='minus';
        }
        div=$('i[data-int='+id+']').parent();
        var Data={};
        Data[modelKey]=dataEntry;
        Data.id=id;
        Data.action=action;
        jQuery.ajax({
            url:addLink,
            language:window.Amer.Language,
            dir:window.Amer.dir,
            dataType:'json',
            crossDomain:true,
            contentType:"application/x-www-form-urlencoded; charset=UTF-8",
            data:Data,
            type:'post',
        }).done(function(data){
            $('i[data-int='+id+']').attr('class','fa fa-'+data[0]);
            console.log(data);
            
            var oClick=`plusminus('${dataEntry}','${id}','${data[0]}','${addLink}','${modelKey}')`;
            $(div).attr('onclick',oClick);
        });
    };
    searchModal=function (input){
        var value=$(input).val();
        //list all vals
        var row=$(input).parent().parent();
        var rowchilds=$(row).children();
        var ul=rowchilds[1];
        var li=$(ul).children();
        $.each(li,function(k,v){
            var licontent=$(v).children()[0];
            var liName=$(licontent).children()[0];
            var Name=licontent.innerText;
            if(Name.includes(value) === false){
                $(v).attr('style','display:none !important');
            }else{
                $(v).attr('style','');
            }
        });
    }
    addPermToRole=function (RoleId,id,action){
        if(action == 'minus'){
            action='plus';
        }else{
            action='minus';
        }
        div=$('i[data-int='+id+']').parent();

        jQuery.ajax({
            url:addPermToRoleLink,
            language:window.Amer.Language,
            dir:window.Amer.dir,
            dataType:'json',
            crossDomain:true,
            contentType:"application/x-www-form-urlencoded; charset=UTF-8",
            type:'post',
            data:{RoleId,id,action},
        }).done(function(data){
            $('i[data-int='+id+']').attr('class','fa fa-'+data[0]);
            $(div).attr('onclick',"addPermToRole("+RoleId+","+id+",'"+data[0]+"')");
        });
    }
    checkListPermissionINIT=(e)=>{
        var uniqueid=generateUUID().split('-')[0]
        $(e).attr('uniqueid',uniqueid)
        window.Amer['permissionList']={};
        window.Amer.permissionList[uniqueid]={};
        window.Amer.permissionList[uniqueid]['alldata']=JSON.parse($(e).attr('data-alldata'));
        window.Amer.permissionList[uniqueid]['permissionFieldName']=$(e).attr('data-permissionFieldName');
        window.Amer.permissionList[uniqueid]['oldDb']=JSON.parse($(e).attr('data-oldDb'));
        var checkall=$(`<div class="col-sm-4">
            <div class="checkbox  py-1 list-group-item list-group-item-primary">
                <label class="font-weight-normal">${jstrans.actions.selectall}</label>
                <input type="checkbox" class="control-input border" id="selectall">
            </div>
        </div>`);
        var loadMore=$(`<div class="col-sm-4">
            <div target="morecheckboxes" class="btn btn-success">
                <label class="font-weight-normal">${jstrans['permissionmanager']['extra_permissions']}</label>
                </div>
            </div>
        </div>`);
        $(e).html(checkall);
        $(e).append(loadMore);
        loadMoreListPermission(uniqueid,'target');
        $('#guard_name').on('change',function(){loadMoreListPermission(uniqueid,'target');})
        $("#selectall").click(function(){$('input:checkbox').not(this).prop('checked', this.checked);});
        loadMore.click(function(el){
            $('[target=morecheckboxes]').remove()
            loadMoreListPermission(uniqueid,'more');}
        );
    };
    loadMoreListPermission=(uniqueid,target)=>{
        //window.Amer.permissionList[uniqueid]
        var alldata=window.Amer.permissionList[uniqueid]['alldata'];
        const old = [...new Set(window.Amer.permissionList[uniqueid]['oldDb'].map(item => item.id))];
        var selectedGuard=$('#guard_name').val();
        var selectedData=alldata.filter(function(data){return data.guard_name == selectedGuard});
        var notSelectedData=alldata.filter(function(data){return data.guard_name !== selectedGuard});
        if(target == 'target'){wantedData=selectedData;}else if(target == 'more'){wantedData=notSelectedData;}
        wantedDataNames=wantedData.map(function(data){
            no=data.name;
            no=no.split('-')[0].trim();
            return no
        });
        wantedDataNames = wantedDataNames.filter(function(item, pos) {
            return wantedDataNames.indexOf(item) == pos;
        });
        $.each(wantedDataNames,function(k,v){
            var mainDiv=$(`<div class="list-group col-sm-6 rounded">${v.name}</div>`);
            var ul=$('<ul class="list-group col-sm-6 rounded"></ul>');
            var group={};
            $.each(wantedData,function(l,m){
                if(m.name.includes(v)){
                    var li=$('<li class="list-group-item list-group-item-action">'+m.name+'</li>')
                    var checked='';
                        if(in_array(old,m.id)){
                            checked='checked'
                        }
                        var insideLi=$(`
                            <input type="checkbox" class="control-input border"
                            name="`+permissionFieldName+`[]"
                            value="`+m.id+`" `+checked+`><label class="font-weight-normal">`+m.name+`</label>
                            `);
                        $(li).html(insideLi);
                    $(ul).append($(li))
                    
                }
                
            });
            //$(col).append($(ul));
            $('.checklist').append(ul);
        });
    };
})(jQuery)

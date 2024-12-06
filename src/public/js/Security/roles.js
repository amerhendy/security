(function(){
    addToRoleBTN=(button,Title,URL)=>{
        var RoleId=$(button).data('entry');
        var ModalId=$(button).attr('data-ModalId');
        var ResultFn=$(button).attr('data-ResultFn');
        var type=$(button).data('buttontype');
        var ModelKey=$(button).attr('data-ModelKey');
        var addLink=$(button).attr('data-addLink');
        createModal(button,ModalId,Title,RoleId,addLink);
        if(type == 'addRolesUsers'){
            var selected=['name','email'];
        }else if(type == 'addRolesPerms'){
            var selected=['name'];
        }
        var Data={};
        Data[ModelKey]=RoleId;
        sendSecRequestToGetAll('all',RoleId,URL,Data,ModalId,ResultFn,selected,addLink);
    };
    if($("[data-button-type=addRolesPerms]") !== undefined){
        $("[data-button-type=addRolesPerms]").unbind('click');
    }
    if($("[data-button-type=addRolesUsers]") !== undefined){
        $("[data-button-type=addRolesUsers]").unbind('click');
    }
    createModal=function(button,id,title,RoleId,addLink){
        fst=$(button).parent().parent().children()[1];
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
            <input type="checkbox" class="control-input border" name="selectall" onclick="checkListPermissionINIT(this)"><label class="font-weight-normal">select All</label>
            <input type="hidden" name="selectallvalues">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        <span class="badge bg-primary rounded-circle" style="display:none !important" data-action="plus" onclick="bulckaction(this,'${id}','plus','${RoleId}','${addLink}')">
                                            <i class="fa fa-plus"></i>
                                        </span>
                                        <span class="badge bg-primary rounded-circle" style="display:none !important" data-action="minus" onclick="bulckaction(this,'${id}','minus','${RoleId}','${addLink}')">
                                            <i class="fa fa-minus"></i>
                                        </span>
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
    sendSecRequestToGetAll=function (Type,RoleId,URL,DATA,DivId,ResultFn,fstText,addLink){  
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
                var vls=[];
                if(typeof fstText == 'object'){
                    $.each(fstText,function(m,n){
                        vls.push(v[n]);
                    });
                }
                var fstDivInnerText=`<li class="list-group-item d-flex justify-content-between align-items-start">
                                        <input type="checkbox" uniqueid="${generateUUID().split('-')[0]}" value="${v['id']}" data-RoleId="${RoleId}" data-action="${plus}" onclick="checkListPermissionINIT(this)">
                                        <div class="ms-2 me-auto">
                                            <div class="fw-bold">`+vls.join('<br>')+`</div>
                                        </div>
                                        <span class="badge bg-primary rounded-circle" onclick="${ResultFn}('${RoleId}','${v['id']}','${plus}','${addLink}')">
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
        $('#'+DivId+'Modal .modal-dialog .modal-content .modal-footer').find('input[name=selectall]')[0].checked=false;
    }
    searchModal=function (input){
        var value=$(input).val();
        //list all vals
        var row=$(input).parent().parent();
        var rowchilds=$(row).children();
        var ul=rowchilds[1];
        var li=$(ul).children();
        $.each(li,function(k,v){
            if(['-','+'].includes(value)){
                if(value == '+'){
                    var lds=$(v).find('.fa-minus');
                }else{
                    var lds=$(v).find('.fa-plus');
                }
                $.each(lds,function(l,m){
                    $(v).attr('style','display:none !important');
                });
            }else{
                var licontent=$(v).children()[1];
                var liName=$(licontent).children()[0];
                var Name=licontent.innerText;
                if(Name.includes(value) === false){
                    $(v).attr('style','display:none !important');
                }else{
                    $(v).attr('style','');
                }
            }
            
        });
    }
    addRolesPerms=function (RoleId,id,action,addLink){
        if(action == 'minus'){
            newaction='plus';
        }else{
            newaction='minus';
        }
        div=$('i[data-int='+id+']').parent();

        jQuery.ajax({
            url:addLink,
            language:window.Amer.Language,
            dir:window.Amer.dir,
            dataType:'json',
            crossDomain:true,
            contentType:"application/x-www-form-urlencoded; charset=UTF-8",
            type:'post',
            data:{RoleId,id,action},
        }).done(function(data){
            if(data.includes(id)){
                var Iclass=`fa fa-${newaction}`;
                $('i[data-int='+id+']').attr('class',Iclass);
                var DIVonclick=`addRolesPerms('${RoleId}','${id}','${newaction}')`;
                $(div).attr('onclick',DIVonclick);
            }
        });
    }
    addRolesUser=function (RoleId,id,action,addLink){
        if(action == 'minus'){
            newaction='plus';
        }else{
            newaction='minus';
        }
        div=$('i[data-int='+id+']').parent();
        jQuery.ajax({
            url:addLink,
            language:window.Amer.Language,
            dir:window.Amer.dir,
            dataType:'json',
            crossDomain:true,
            contentType:"application/x-www-form-urlencoded; charset=UTF-8",
            data:{RoleId,id,action},
            type:'post',
        }).done(function(data){
            if(data.includes(id)){
                var Iclass=`fa fa-${newaction}`;
                $('i[data-int='+id+']').attr('class',Iclass);
                var DIVonclick=`addRolesUser('${RoleId}','${id}','${newaction}')`;
                $(div).attr('onclick',DIVonclick);
            }
        });
    }
    bulckaction=(e,modelID,action,RoleId,addLink)=>{
        //if(modelID == 'addRolesUser'){var Dlink=addUsersToRoleLink;}else if(modelID == 'addRolesPerms'){var Dlink=addPermToRoleLink;}
        var Dlink=addLink;
        if(action == 'minus'){
            newaction='plus';
        }else{
            newaction='minus';
        }
        console.log(action);
        
        var ids=e.parentElement.children[2].value;
        ids=JSON.parse(ids);
        jQuery.ajax({
            url:Dlink,
            language:window.Amer.Language,
            dir:window.Amer.dir,
            dataType:'json',
            crossDomain:true,
            contentType:"application/x-www-form-urlencoded; charset=UTF-8",
            data:{RoleId,id:ids,action},
            type:'post',
        }).done(function(data){
            $.each(data,function(k,v){
                var Iclass=`fa fa-${newaction}`;
                $('i[data-int='+v+']').attr('class',Iclass);
                var div=$('i[data-int='+v+']')[0].parentElement;
                var DIVonclick=`${modelID}('${RoleId}','${v}','${newaction}')`;
                $(div).attr('onclick',DIVonclick);
            });
            return;
        });
        
    };
    checkListPermissionINIT=(e)=>{
        var listgroup=$(e).parents().find('ul[class*=list-group]');
        var listli=$(listgroup).find('li');
        var targetcheckboxes=[];
        var wantedCheckboxes=[];
        var promot=[];
        if($(e).name() == 'selectall'){
            var footerDiv=e.parentElement;
        }else{
            var footerDiv=e.parentElement.parentElement.parentElement.parentElement.parentElement.children[2];
        }
            var inputext=footerDiv.children[2];
            var addBTN=footerDiv.children[4];
            var removeBTN=footerDiv.children[5];
        $.each(listli,function(k,v){
            if(v.style.display == ''){
                targetcheckboxes.push($(v).find('input[type=checkbox]'));
            }
        });
        if($(e).name() == 'selectall'){
            var stts=e.checked;
            $.each($(listgroup).find('input[type=checkbox]'),function(k,v){
                v.checked=false;
            });
            if(e.checked == true){
                $.each($(targetcheckboxes),function(k,v){
                    v[0].checked=true;
                });
            }
            
        }
        $.each(targetcheckboxes,function(k,v){
            if(v[0].checked == true){
                //remove from targetcheckboxes
                wantedCheckboxes.push(v[0]);
            }
        });
        $.each(wantedCheckboxes,function(k,v){
            var prent=$(v).parent();
            var prent=$(prent).find('.badge');
            var ResultFn=$(prent).attr('onclick');
                ResultFn=ResultFn.split('(')[0];
            var id=v.value;
            var roleid=$(v).data('roleid');
            var action=$(v).data('action');
            promot.push(id);
        });
        if(promot.length == 0){
            inputext.value="";
            $(addBTN).attr('style','display:none !important');
            $(removeBTN).attr('style','display:none !important');
        }else{
            $(addBTN).attr('style','');
            $(removeBTN).attr('style','');
            inputext.value=JSON.stringify(promot);
        }
        return;
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

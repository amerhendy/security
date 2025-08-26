<a href="javascript:void(0)" onclick="addTeamsUsers(this)" data-entry="{{$entry->getKey()}}" data-mdb-ripple-duration="0s" class="btn btn-sm btn-success" data-button-type="addteamUser"><i class="fa fa-users"></i></a>
@push('after_scripts') @if (request()->ajax()) @endpush @endif
@loadOnce('addTeamsUsers')
<script>
    if (typeof addTeamsUsers != 'function') {
        $("[data-button-type=addteamUser]").unbind('click');
        function addTeamsUsers(button){
            var TeamId=$(button).data('entry')
            var type='all';
            var ModalId='adduserToTeam';
            var ResultFn='adduserToTeam';
            createModal(button,ModalId,"{{trans('SECLANG::Teams.User')}}");
            sendSecRequestToGetAll('all',TeamId,"{{url('Security/Teams/fetch/Users')}}",{type,TeamId},ModalId,ResultFn,'name','email');
        }
        function adduserToTeam(TeamId,id,action){
            if(action == 'minus'){
                action='plus';
            }else{
                action='minus';
            }
            div=$('i[data-int='+id+']').parent();

            jQuery.ajax({
                url:"{{url('Security/Teams/fetch/AddUsers')}}",
                data:{TeamId,id,action},
				type:'post',
            }).done(function(data){
                $('i[data-int='+id+']').attr('class','fa fa-'+data[0]);
                $(div).attr('onclick',"adduserToTeam("+TeamId+","+id+",'"+data[0]+"')");
            });
        }
    }
</script>
@endLoadOnce
@loadOnce('sendSecRequestToGetAll')
<script>
function sendSecRequestToGetAll(Type,RoleId,URL,DATA,DivId,ResultFn,fstText,ndText=null){
    var ndText=ndText;
    jQuery.ajax({
        url:URL,
        data:DATA,
        type:'post',
    }).done(function(data){
        var allData=data[1];
        var selectedDataIds=data[0]
        var divFst=$(`<div class="row"></div>`);
        var col=$(`<div class="mb-3"><label class="form-label" for="search">search</label></div>`);
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
                                    <span class="badge bg-primary rounded-circle" onclick="`+ResultFn+`(`+RoleId+`,`+v['id']+`,'`+plus+`')">
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
</script>
@endLoadOnce
@loadOnce('searchModal')
<script>
    function searchModal(input){
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
</script>
@endLoadOnce
@loadOnce('CreateModal')
<script>
    function createModal(button,id,title){
            fst=$(button).parent().parent().children()[0];
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
</script>
@endLoadOnce
@if (!request()->ajax()) @endpush @endif


function showReplyForm(id) {
    d = document.getElementsByClassName("ReplyForm");
    for( i = 0; i < d.length; i++){
        d[i].style.display = "none";
    }
    document.getElementById(id).style.display = "";

// TEMP
    v=document.getElementById('q_id').value;
    document.getElementById('qid_'+id).value=v;

}

function correction(v, id) {
    if(v>=1&&confirm('Подтвердите изменение оценки')) document.getElementById("crr_"+id).value = v;
    else return false;
}

function get_question(s,qid) {
    document.getElementById('comm_text_value').value = '"'+ s + '"' + " - \n";
//    document.getElementById('comm_text').value = '"'+ s + '"' + " - \n";
    document.getElementById('comm_text').focus();
    document.getElementById('q_id').value=qid;
    document.getElementById('comm_text').disabled="";

}


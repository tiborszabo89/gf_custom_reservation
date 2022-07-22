document.addEventListener('DOMContentLoaded', () => {
  gform.addAction( 'gform_input_change', function( elem, formId, fieldId ) {
    if( ! window.gf_form_conditional_logic ) {
        return;
    }
    var dependentFieldIds = rgars( gf_form_conditional_logic, [ formId, 'fields', gformExtractFieldId( fieldId ) ].join( '/' ) );
    if( dependentFieldIds ) {
        gf_apply_rules( formId, dependentFieldIds );
    }
  }, 10 );
  
  //console.log('test')
}) 

function keypressZipCode(fn) {
  document.addEventListener("DOMContentLoaded", fn);
  if (document.readyState === "interactive" || document.readyState === "complete" ) {
    fn();
  }
}


keypressZipCode(() => {
  var formid = main_form_id_def.MAIN_FORM;
  var zipCodeInput = document.getElementById(`input_${formid}_4_5`)
  var fileUploadInput = document.getElementById(`field_${formid}_5`)
  var placeSelect = document.getElementById(`field_${formid}_6`)
  var timeslotSelect = document.getElementById(`field_${formid}_7`)
  var textArea = document.getElementById(`field_${formid}_10`)
  var consent_1 = document.getElementById(`field_${formid}_11`)
  var consent_2 = document.getElementById(`field_${formid}_12`)

  var mainBtn = document.getElementById(`gform_submit_button_${formid}`)
  if(fileUploadInput) {
    fileUploadInput.classList.add('d-none');
    placeSelect.classList.add('d-none');
    mainBtn.classList.add('d-none');
    timeslotSelect.classList.add('d-none');
    textArea.classList.add('d-none');
    consent_1.classList.add('d-none');
     consent_2.classList.add('d-none');
  }
  if (zipCodeInput) {
    zipCodeInput.addEventListener('keyup', function(e) {
      zipCodeValue = e.target.value
      zipCodeValidationArray = ['44787', '44807', '44789', '44892', '44879', '44869', '44867', '44866', '44809', '44894', '44805', '44801', '44799', '44797', '44795', '44793', '44791', '44803', '44581', '44575', '44579', '44577', '44329', '44309', '44319', '44289', '44328', '44388', '44339', '44357', '44359', '44369', '44379', '44269', '44287', '44225', '44267', '44143', '44265', '44137', '44139', '44141', '44135', '44145', '44149', '44227', '44229', '44263', '44147', '44649', '44653', '44652', '44651', '44628', '44629', '44627', '44625', '44623', '44532', '44534', '44536', '45711', '45257', '45127', '45359', '45357', '45356', '45355', '45329', '45327', '45326', '45309', '45307', '45289', '45279', '45277', '45276', '45259', '45239', '45138', '45128', '45130', '45131', '45133', '45134', '45219', '45136', '45139', '45141', '45143', '45144', '45145', '45147', '45149', '45899', '45897', '45896', '45894', '45891', '45892', '45886', '45889', '45888', '45884', '45883', '45881', '45879', '45964', '45966', '45968', '45721', '45525', '45527', '45529', '45699', '45701', '45772', '45770', '45768', '45476', '45481', '45478', '45479', '45475', '45473', '45472', '45470', '45468', '45739', '45657', '45659', '45661', '45663', '45665', '45549', '45731', '46519', '46236', '46238', '46240', '46242', '46244', '46539', '46535', '46537', '46282', '46284', '46286', '46499', '46045', '46047', '46049', '46117', '46119', '46145', '46147', '46149', '46562', '46483', '46485', '46487', '46569', '46514', '46509', '47665', '47178', '47279', '47269', '47259', '47249', '47239', '47229', '47228', '47226', '47199', '47051', '47179', '47198', '47169', '47059', '47053', '47055', '47167', '47058', '47057', '47119', '47137', '47138', '47139', '47166', '47475', '47441', '47443', '47445', '47447', '47506', '47495', '58339', '58256', '58730', '58285', '58097', '58135', '58119', '58099', '58095', '58093', '58091', '58089', '58313', '58332', '58239', '58300', '58452', '58453', '58454', '58455', '58456', '59071', '59075', '59073', '59077', '59069', '59067', '59065', '59063', '59439', '59174', '59423', '59425', '59427', '59368', '59199', '59192', '58730', '59439', '59174', '59379'];
      if (zipCodeValue) {
        var fileUploadInput = document.getElementById(`field_${formid}_5`)
        var placeSelect = document.getElementById(`field_${formid}_6`)
        var timeslotSelect = document.getElementById(`field_${formid}_7`)
        var mainBtn = document.getElementById(`gform_submit_button_${formid}`)
        var textArea = document.getElementById(`field_${formid}_10`)
         var consent_1 = document.getElementById(`field_${formid}_11`)
         var consent_2 = document.getElementById(`field_${formid}_12`)
        if ((zipCodeValidationArray.indexOf(zipCodeValue) >= 0)) {
          fileUploadInput.classList.remove('d-none');
          placeSelect.classList.remove('d-none');
          mainBtn.classList.remove('d-none');
          //timeslotSelect.classList.remove('d-none');
          textArea.classList.remove('d-none');
          consent_1.classList.remove('d-none');
          consent_2.classList.remove('d-none');
          
        } else {
          fileUploadInput.classList.add('d-none');
          placeSelect.classList.add('d-none');
          mainBtn.classList.add('d-none');
          timeslotSelect.classList.add('d-none');
          textArea.classList.add('d-none');
           consent_1.classList.add('d-none');
           consent_2.classList.add('d-none');
        }
      }
    });
  }
})

function keypressZipCode_2(fn) {
  document.addEventListener("DOMContentLoaded", fn);
  if (document.readyState === "interactive" || document.readyState === "complete" ) {
    fn();
  }
}


keypressZipCode_2(() => {
  var formid = main_form_id_def.PRE_FORM;
  var zipCodeInput = document.getElementById(`field_${formid}_3`)
  var consent = document.getElementById(`field_${formid}_4`)
  var mainBtn = document.getElementById(`gform_submit_button_${formid}`)
  if(consent) {
    consent.classList.add('d-none');
    mainBtn.classList.add('d-none');
  }
  if (zipCodeInput) {
    zipCodeInput.addEventListener('keyup', function(e) {
      zipCodeValue = e.target.value
      zipCodeValidationArray = ['44787', '44807', '44789', '44892', '44879', '44869', '44867', '44866', '44809', '44894', '44805', '44801', '44799', '44797', '44795', '44793', '44791', '44803', '44581', '44575', '44579', '44577', '44329', '44309', '44319', '44289', '44328', '44388', '44339', '44357', '44359', '44369', '44379', '44269', '44287', '44225', '44267', '44143', '44265', '44137', '44139', '44141', '44135', '44145', '44149', '44227', '44229', '44263', '44147', '44649', '44653', '44652', '44651', '44628', '44629', '44627', '44625', '44623', '44532', '44534', '44536', '45711', '45257', '45127', '45359', '45357', '45356', '45355', '45329', '45327', '45326', '45309', '45307', '45289', '45279', '45277', '45276', '45259', '45239', '45138', '45128', '45130', '45131', '45133', '45134', '45219', '45136', '45139', '45141', '45143', '45144', '45145', '45147', '45149', '45899', '45897', '45896', '45894', '45891', '45892', '45886', '45889', '45888', '45884', '45883', '45881', '45879', '45964', '45966', '45968', '45721', '45525', '45527', '45529', '45699', '45701', '45772', '45770', '45768', '45476', '45481', '45478', '45479', '45475', '45473', '45472', '45470', '45468', '45739', '45657', '45659', '45661', '45663', '45665', '45549', '45731', '46519', '46236', '46238', '46240', '46242', '46244', '46539', '46535', '46537', '46282', '46284', '46286', '46499', '46045', '46047', '46049', '46117', '46119', '46145', '46147', '46149', '46562', '46483', '46485', '46487', '46569', '46514', '46509', '47665', '47178', '47279', '47269', '47259', '47249', '47239', '47229', '47228', '47226', '47199', '47051', '47179', '47198', '47169', '47059', '47053', '47055', '47167', '47058', '47057', '47119', '47137', '47138', '47139', '47166', '47475', '47441', '47443', '47445', '47447', '47506', '47495', '58339', '58256', '58730', '58285', '58097', '58135', '58119', '58099', '58095', '58093', '58091', '58089', '58313', '58332', '58239', '58300', '58452', '58453', '58454', '58455', '58456', '59071', '59075', '59073', '59077', '59069', '59067', '59065', '59063', '59439', '59174', '59423', '59425', '59427', '59368', '59199', '59192', '58730', '59439', '59174', '59379'];
      if (zipCodeValue) {
        var consent = document.getElementById(`field_${formid}_4`)
        var mainBtn = document.getElementById(`gform_submit_button_${formid}`)
        if ((zipCodeValidationArray.indexOf(zipCodeValue) >= 0)) {
          consent.classList.remove('d-none');
          mainBtn.classList.remove('d-none');
        } else {
          consent.classList.add('d-none');
          mainBtn.classList.add('d-none');
        }
      }
    });
  }
})


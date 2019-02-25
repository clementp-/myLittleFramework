$(function(){

  ////////////////////////////////////////////
  // Exemple
  ////////////////////////////////////////////
  $('body').on('click', 'div', function(){
    console.log(this);
  });


  ////////////////////////////////////////////
  // Efface les messages d'information apres 3secondes
  ////////////////////////////////////////////
  setTimeout(function() {
    $('.msgs').hide();
  }, 3000);



  ////////////////////////////////////////////
  // Demo slide
  ////////////////////////////////////////////
  $('.slide').slick();

  $('.multiple-items').slick({
    infinite: true,
    slidesToShow: 6,
    slidesToScroll: 3
  });






});

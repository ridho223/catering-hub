document.addEventListener("DOMContentLoaded",() => {
    const table = document.querySelector("#datatable");

    if (table && typeof DataTable !== "undefined") {
        new DataTable("#datatable");
    }


    document.querySelectorAll(".btn-delete").forEach(button =>{
        button.addEventListener("click",function(e){
            e.preventDefault();

            const form = this.closest("form");

            if (!form) return;

            if(typeof Swal !== "undefined"){
                Swal.fire({
                    title:"Hapus Data?",
                    text:"Data Tidak Bisa Dikembalikan.",
                    icon:"warning",
                    showCancelButton:true,
                    confirmButtonText:"Hapus",
                    cancelButtonText:"Batal",            
                }).then((result) => {
            if(result.isConfirmed){
                form.submit();
            }
            });
      }else{
        form.submit();
      }

    });
});

    const image = document.getElementById('image');

    if(image){

        image.onchange = function(){

            const preview = document.getElementById('preview');

            if(!preview) return;

            const reader = new FileReader();
            reader.onload = function(e){
                preview.src = e.target.result;
            };

             reader.readAsDataURL(image.files[0]);
        };

    }

});
// const preview = document.getElementById('preview');

// image.onchange = function(){
//     const reader = new fileReader();
//     reader.onload = function(e){
//         preview.src=e.target.result;
//     }

//     reader.readAsDataURL(image.files[0]);

// }
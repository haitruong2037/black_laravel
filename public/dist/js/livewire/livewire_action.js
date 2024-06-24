document.addEventListener( 'livewire:init', () => {
    document.querySelectorAll( '[data-livewire-action="view_comment_details"]' ).forEach( function ( element ) {
        element.addEventListener( 'click', function () {
            Livewire.dispatch( 'view_comment_details', {id: this.getAttribute( 'data-id' )} );
        } );
    } );

    Livewire.on( 'comment-update-hidden', (e) => {
        const commentElement = document.querySelector( `.comment-hidden-button[data-comment-hidden-id="${e.commentId}"]`);
        if (commentElement) {
            commentElement.textContent = e.hidden == 1 ? 'Hidden' : 'Showing';
            commentElement.classList.remove( 'btn-outline-danger', 'btn-outline-success' ); 
            commentElement.classList.add( e.hidden == 1 ? 'btn-outline-danger' : 'btn-outline-success' );
        }
    } );

})
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        @if(count($books))
            <div class="max-w-8xl mx-auto sm:px-2 lg:px-4">
                <button id="sort-az" class="btn btn-primary" onClick="sort_az(true)">Sort Title A-Z </button>
                <button id="sort-za" class="btn btn-primary" onClick="sort_az(false)">Sort Title Z-A </button>
                <button id="sort-az" class="btn btn-primary" onClick="sort_pagecount(true)">Sort PageCount <i class="fa fa-arrow-up"></i></button>
                <button id="sort-za" class="btn btn-primary" onClick="sort_pagecount(false)">Sort PageCount <i class="fa fa-arrow-down"></i></button>
            </div>
        @endif
        <div class="max-w-8xl mx-auto sm:px-2 lg:px-4">
            <div class="overflow-hidden shadow-sm sm:rounded-lg" id="book-list">
                    @forelse ($books as $book)
                    <div id="index-{{ $loop->index }}" data-id="{{$book->id}}" class="book-card-{{$book->id}} book-card p-10  border-b border-gray-200" data-title="{{$book->title}}" data-pagecount="{{$book->page_count}}">
                        <section id="{{$book->google_id}}" class="flex flex-col md:flex-row gap-11 py-10 px-5 bg-white rounded-md shadow-lg w-3/4 md:max-w-2xl" style="flex-direction: column">
                            <div class="text-indigo-500 flex flex-col justify-between">
                                <img src="{{$book->small_thumbnail}}" alt="" width="200">
                            </div>
                            <div class="text-indigo-500">
                                <h3 class="uppercase text-black text-2xl font-medium">{{ $book->title }} This is a book title</h3>

                                @if(isset($book->subtitle))
                                    <small class="uppercase">{{ $book->subtitle}}</small>
                                    <br>
                                @endif
                                <a class="btn btn-primary" data-toggle="collapse" href="#multiCollapseExample-{{$book->id}}" role="button" aria-expanded="false" aria-controls="multiCollapseExample1">See description</a>
                                <div class="row">
                                    <div class="col">
                                        <div class="collapse multi-collapse" id="multiCollapseExample-{{$book->id}}">
                                            <div class="card card-body">
                                                <small class="text-black">{!! $book->description !!}</small>
                                            </div>
                                        </div>
                                    </div>

                                </div>



                                <p class="text-black text-2l font-medium "><i>Page Count: {{ $book->page_count }}</i></p>
                                <div class="flex gap-0.5 mt-4">
                                    @if(isset($book->volumeInfo->previewLink ))
                                        <a href="{{$book->volumeInfo->previewLink }}" id="likeButton" class="bg-blue-600 hover:bg-blue-500 focus:outline-none transition text-white uppercase p-2">
                                            Read on google
                                        </a>
                                    @endif

                                    <button id="likeButton" class="bg-red-600 hover:bg-red-500 focus:outline-none transition text-white uppercase p-2" data-id="{{ $book->id }}" onclick="deleteBook(this)">
                                        remove from list
                                    </button>
                                </div>


                            </div>

                        </section>

                    </div>
                    @empty
                    <div class="p-6 bg-white border-b border-gray-200 text-center">
                        <h3 class="text-grey-500 text-2xl">You currently have no books in your list. Try searching for some that you might like!</h3><br>

                        <a href="/book?search" class="btn btn-primary">Search Books</a>
                    </div>
                    @endforelse

            </div>
        </div>
    </div>


    <script>
        new Sortable(document.getElementById("book-list"), {
            animation: 150,
            ghostClass: 'sortable-ghost',
            onEnd: function (/**Event*/evt) {
                var itemEl = evt.item;  // dragged HTMLElement
                console.log(evt.oldIndex, evt.newIndex);
                var oldIndexId = "index-" + evt.oldIndex;
                var newIndexId = "index-" + evt.newIndex;
                var oEl = $("#"+oldIndexId);
                var nEl = $("#"+newIndexId);
                let book1_id = oEl.attr("data-id");
                let book2_id = nEl.attr("data-id");

                oEl.attr("id", newIndexId);
                nEl.attr("id", oldIndexId);

                ajaxSetup();

                $.ajax({
                    type: "PUT",
                    url: "/book/" + book1_id,
                    data: {
                        book_2: book2_id
                    },
                    error: function (data) {
                        // log data
                    }
                });
            },
        });

        var list = document.getElementById('book-list');

        // Main function
        function sort_az(up)
        {
            var cards = $('.book-card');

            if(up) {
                cards.sort(function(a, b)
                {
                    return ($(b).text().toUpperCase()) <
                    ($(a).text().toUpperCase()) ? 1 : -1;
                });
            } else {
                cards.sort(function(a, b)
                {
                    return ($(b).text().toUpperCase()) >
                    ($(a).text().toUpperCase()) ? 1 : -1;
                });
            }


            cards.appendTo(document.getElementById("book-list"));
        }

        function sort_pagecount(up)
        {

            var cards = $('.book-card');

            if(up) {
                cards.sort(function(a, b)
                {
                    console.log(a);
                    console.log(b);
                    return +$(a).attr('data-pagecount') -
                        +$(b).attr('data-pagecount');
                });
            } else {
                cards.sort(function(a, b)
                {
                    return +$(b).attr('data-pagecount') -
                        +$(a).attr('data-pagecount');
                });
            }

            cards.appendTo(document.getElementById("book-list"));
        }

        function deleteBook(obj) {
            var bookId = $(obj).attr('data-id');
            var bookCard = $(".book-card-"+bookId);

            swal({
                title: "Are you sure?",
                text: "This is forever",
                icon: "warning",
                buttons: {
                    cancel: {
                        text: "Cancel",
                        value: null,
                        visible: true,
                        className: "",
                        closeModal: true,
                    },
                    confirm: {
                        text: "Remove",
                        value: true,
                        visible: true,
                        // className: "btn btn-danger",
                        closeModal: true,
                        dangerMode: true
                    }
                },
                confirmButtonColor: '#DD6B55',

            }).then((willDelete) => {
                if (willDelete) {
                    bookCard.fadeOut(500);
                    setTimeout(function(){
                        bookCard.remove();
                    },1000);

                    ajaxSetup();

                    $.ajax({
                        type: "DELETE",
                        url: "/book/" + bookId,
                        error: function (data) {
                            swal("There was a problem deleting the file!");
                        }
                    });
                } else {
                    swal("Cancelled. But not dogecoin though. .Dogecoin to the mooooooooon!!!");
                }

            });

        }

        function ajaxSetup() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                }
            });
        }



    </script>

    @push('scripts')

    @endpush
</x-app-layout>

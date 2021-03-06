<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight text-center">
            {{ __('Book Search') }}
        </h2>
    </x-slot>
    @if(!isset($query))
        <div class="py-12">

            <div class="max-w-8xl mx-auto sm:px-2 lg:px-4">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="max-w-8xl mx-auto sm:px-2 lg:px-4 p-5">
                        <form action="/book" method="GET">
                            <input type="text" name="q" class="form-control" placeholder="Search keywords, categories, author names etc...">
                            <br>
                            <button class="btn btn-primary" type="submit">Find your book</button>

                        </form>
                    </div>

                </div>
            </div>
        </div>
    @else
        <div class="py-12">

            <div class="max-w-8xl mx-auto sm:px-2 lg:px-4">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="max-w-8xl mx-auto sm:px-2 lg:px-4 p-5">
                        <form action="/book" method="GET">
                            <input type="text" name="q" class="form-control" placeholder="Search keywords, categories oauthor names etc..." value="{{$query ?? ""}}">
                            <br>
                            <button class="btn btn-primary" type="submit">Find your book</button>

                        </form>
                    </div>

                </div>
            </div>

        <div class="max-w-8xl mx-auto sm:px-2 lg:px-4">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="max-w-8xl mx-auto sm:px-2 lg:px-4">
                    <h3 class="text-indigo-500 p-3">Your search returned {{$total ?? ''}} {{$total == 1 ? "result" : "results"}} | {{$pages}} pages </h3>
                    @if($pages ?? 0 > 1)
                        <div class="p-3">
                            @if($currentPage == 1)
                                <a>{{$currentPage}}</a> | <a href="/book?q={{$query}}&startIndex={{$startIndex+30}}"> next page <i><i class="fa fa-arrow-right"></i></i></a>
                            @elseif($currentPage > 1)
                                <a href="/book?q={{$query}}&startIndex={{$startIndex-30}}"><i class="fa fa-arrow-left"></i></a>
                                | <a>{{$currentPage}}</a> |
                                <a href="/book?q={{$query}}&startIndex={{$startIndex+30}}"> next page <i><i class="fa fa-arrow-right"></i></i></a>
                            @endif
                        </div>

                    @endif
                </div>

                <hr>

                @forelse ($books ?? '' as $book)
                    <div class="p-10 bg-white border-b border-gray-200">
                        <section class="flex flex-col md:flex-row gap-11 py-10 px-5 bg-white rounded-md shadow-lg w-3/4 md:max-w-2xl">
                            <div class="text-indigo-500 flex flex-col justify-between">
                                <img src="{{$book->volumeInfo->imageLinks->smallThumbnail ?? ""}}" alt="">
                            </div>
                            <div class="text-indigo-500">
                                <h3 class="uppercase text-black text-2xl font-medium">{{ $book->volumeInfo->title }} </h3>
                                <p class="">{{ isset($book->volumeInfo->authors) ? "Written by " . implode(", ", $book->volumeInfo->authors) : "" }} </p>
                                <br>
                                <div class="flex gap-0.5 mt-4">
                                    <a id="book-details" href="/book/{{$book->id}}" class="bg-indigo-600 hover:bg-indigo-500 focus:outline-none transition text-white uppercase px-8 py-3">See Details</a>
                                    <form id="book-form-{{$book->id}}">
                                        <input name="google_id" type="hidden" value="{{$book->id}}">
                                        <input name="self_link" type="hidden" value="{{$book->selfLink}}">
                                        <input name="title" type="hidden" value="{{$book->volumeInfo->title}}">
                                        <input name="authors" type="hidden" value="{{isset($book->volumeInfo->authors) ? implode(", ", $book->volumeInfo->authors) : ""}}">
                                        <input name="small_thumbnail" type="hidden" value="{{$book->volumeInfo->imageLinks->smallThumbnail ?? ""}}">
                                        <input name="thumbnail" type="hidden" value="{{$book->volumeInfo->imageLinks->thumbnail ?? ""}}">
                                        <input name="language" type="hidden" value="{{$book->volumeInfo->language ?? "n/a"}}">
                                        <input name="description" type="hidden" value="{{$book->volumeInfo->description ?? "no description"}}">
                                        <input name="page_count" type="hidden" value="{{$book->volumeInfo->pageCount ?? ""}}">
                                        <button type="button" id="bookSubmitButton" class="submit-book bg-green-600 hover:bg-indigo-500 focus:outline-none transition text-white uppercase p-3" onclick="submitBook(this)" data-book-form-id="book-form-{{$book->id}}">
                                            + Add to your list
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </section>
                    </div>
                @empty
                    <div class="p-6 bg-white border-b border-gray-200">
                        <p>No Results for "<i>{{$query}}</i> "</p>
                    </div>
                @endforelse

            </div>
        </div>
    </div>
    @endif

    <script>
        function submitBook(obj) {
            ajaxSetup();
            let formId = $(obj).attr("data-book-form-id");
            let form = $("#"+formId);
            $.ajax({
                type: "POST",
                url: "/book",
                data: form.serialize(),
                success: function(data){
                    var res = data;
                    console.table(data);
                    swal("Added successfully!");
                    $(obj).hide();
                },
                error: function (data) {
                    swal(JSON.parse(data.responseText).warning);
                }
            });
        }

    </script>
</x-app-layout>
<?php

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

        <!-- Styles -->

        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

        <style>
           html,
           body {
            height: 100%;
           }

           body {
            display: flex;
            flex-direction: column;
           }

           footer {
            flex: 0 0 auto;
           }
        </style>

        <script src = "https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    </head>
    <body class="antialiased">

        <ul class="nav nav-tabs">
            <!-- Первая вкладка (активная) -->
            <li class="nav-item">
              <a class="nav-link active" data-toggle="tab" href="#import">Импорт статей</a>
            </li>
            <!-- Вторая вкладка -->
            <li class="nav-item">
              <a class="nav-link" data-toggle="tab" href="#search">Поиск</a>
            </li>
          </ul>
          <div class="container py-5">

          <div class="tab-content">
            <div class="tab-pane fade show active" id="import">
                <form class="form-inline py-3" id="importForm" name="importForm" method="GET">
                    <div class="form-group mx-sm-3 mb-2">
                        <label for="inputPassword2" class="sr-only"></label>
                        <input type="text" class="form-control " name="title" placeholder="Введите название статьи" value="">
                    </div>
                    <button type="button" class="btn btn-primary mb-2" name="importButton" onclick="importArticle()">Импортировать</button>
              </form>

              <div class="form-group py-3">
                <label for="importReporting">Отчёт об импорте</label>
                <textarea class="form-control" id="importReporting" name="importReporting" rows="7"></textarea>
              </div>

              <div class="py-3">
                <table class="table table-striped table-responsive-md">
                    <thead>
                    <tr>
                        <th>Название статьи</th>
                        <th>Ссылка</th>
                        <th>Размер статьи</th>
                        <th>Количество слов</th>
                    </tr>
                    </thead>
                    <tbody id="table">
                        @isset($articles)
                        @foreach ($articles as $article)
                        <tr>
                            <td>{{$article->title}}</td>
                            <td>{{$article->link}}</td>
                            <td>{{$article->size}}</td>
                            <td>{{$article->word_quantity}}</td>
                        </tr>
                        @endforeach
                        @endisset
                    </tbody>
                </table>
              </div>

            </div>
            <div class="tab-pane fade" id="search">
                <div class="form-inline py-3"  method="GET">
                    @csrf
                    <div class="form-group mx-sm-3 mb-2">
                  <label for="inputPassword2" class="sr-only"></label>
                  <input type="text" class="form-control " name="word" placeholder="слово" value="">
                </div>
                <button type="button" class="btn btn-primary mb-2" onclick="searchArticles()">Найти</button>
            </div>

              <div class="row py-5">
                <div class="col align-self-top">
                    <table class="table table-striped"  id="searchTableHead">
                        <thead>
                        <tr>
                            <th>Название статьи</th>
                            <th>Количество вхождений</th>
                        </tr>
                        </thead>
                        <tbody id="searchTable">
                        </tbody>
                    </table>
                </div>
                <div class="col align-self-top">
                    <div class="form-group">
                        <label for="articleTitle">Название статьи</label>
                        <input type="text" class="form-control" id="articleTitle">
                      </div>
                      <div class="form-group">
                        <label for="articleLink">Ссылка на статью</label>
                        <input type="text" class="form-control" id="articleLink">
                      </div>
                      <div class="form-group">
                        <label for="articleText">Текст статьи</label>
                        <textarea class="form-control" id="articleText" rows="10"></textarea>
                      </div>
                </div>
              </div>
            </div>
          </div>
    </div>
    <footer class="footer mt-auto py-3 bg-dark">
        <p class="text-muted text-center">Laravel v{{ Illuminate\Foundation\Application::VERSION }} (PHP v{{ PHP_VERSION }})</p>
      <div class="container">
        <p class="text-muted text-center">&copy Первушин О. С. 2023 г.</p>
      </div>
    </footer>

    <script>
        $(document).ready(function() {
            $("#importForm").keydown(function(event) {
                if(event.keyCode == 13) {
                    event.preventDefault();
                    return false;
                }
            })
        });

        function shadowReload() {
            $.ajax( {
                url: 'wiki/load',
                method: 'GET',
                success:function(response) {
                    console.log(response);

                    if(response) {
                        $("#table").html('');

                        response.forEach(element => {
                            $("#table").append(renderTable(element));
                        });
                    }
                },
                error:function (response) {
                    console.log(response + "error");
                }
            });
        }

        function importArticle() {
            const start = new Date().getTime();

            $('input[name=title]').attr('readonly', true);
            $('button[name=importButton]').attr('disabled', true);;

            let title = $('input[name=title]').val();
            let _token = $('meta[name="csrf-token"]').attr('content');

            $.ajax( {
                url: 'wiki/' + title,
                method: 'GET',
                data: {
                    title: title
                },
                success:function(response) {


                    if(response) {
                        $('.success').text(response.success);
                        $('input[name=title]').attr('readonly', false);
                        $('button[name=importButton]').attr('disabled', false);;
                        const end = new Date().getTime();
                        $('#importReporting').val('Импорт завершен!\n\nНайдена статья по адресу ' + response.link + '\nВремя обработки: ' + (end-start) + ' мс' + '\nРазмер статьи: ' + response.size + ' байт\nКоличество слов: ' + response.word_quantity);
                        $("#importForm")[0].reset();
                        $("#table").append(renderTable(response));
                    }
                },
                error:function (response) {
                    $('input[name=title]').attr('readonly', false);
                    $('button[name=importButton]').attr('disabled', false);
                    const end = new Date().getTime();
                    console.log(response + "error");
                    $('#importReporting').val('Импорт прерван!\n\nНе найдена статья по адресу ' + ('https://ru.wikipedia.org/wiki/' + title).replace(' ', '_') + '\nВремя обработки: ' + (end-start) + ' мс');
                }
            });
        }

        function renderTable(item) {
            return `<tr>
                        <td>${item.title}</td>
                        <td>${item.link}</td>
                        <td>${item.size}</td>
                        <td>${item.word_quantity}</td>
                    </tr>`
        }

        function searchArticles() {
            let word = $('input[name=word]').val();
            let _token = $('meta[name="csrf-token"]').attr('content');

            $.ajax( {
                url: 'wiki/search/' + word,
                method: 'GET',
                data: {
                    word: word
                },
                success:function(response) {
                    console.log(response);

                    if(response) {
                        $("#searchTableHead").attr('display', 'block');
                        $("#searchTable").html('');

                        response.forEach(item => {
                            $("#searchTable").append(renderSearchTable(item));
                        });
                    }
                },
                error:function (response) {
                    console.log(response + " error");
                    $("#searchTable").html('');
                }
            });
        }

        function renderSearchTable(item) {
            return `<tr>
                                <td><button type="button" class="btn btn-link" onclick="renderArticle(${item.id})">${item.title}</button></td>
                                <td>${item.pivot.quantity}</td>

                    </tr>`
        }

        function renderArticle(id) {
            $.ajax( {
                url: '/wiki/article/' + id,
                method: 'GET',
                data: {
                    id: id
                },
                success:function(response) {
                    console.log(response);

                    if(response) {
                        $("#articleTitle").val(response.title);
                        $("#articleLink").val(response.link)
                        $("#articleText").html(response.content);
                    }
                },
                error:function (response) {
                    console.log(response + "error");
                }
            });

        }
    </script>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    </body>
</html>

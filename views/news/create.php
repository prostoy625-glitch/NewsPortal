<h1>Илова кардани хабар</h1>

<form id="news-create-form" method="post" action="#" novalidate>

    <div>
        <label for="news-title">Сарлавҳаи хабар *</label>

        <input
            type="text"
            id="news-title"
            name="title"
            maxlength="200"
            required
            aria-describedby="news-title-error"
        >

        <div id="news-title-error"></div>
    </div>

    <div>
        <label for="news-excerpt">Тавсифи кӯтоҳ *</label>

        <textarea
            id="news-excerpt"
            name="excerpt"
            maxlength="500"
            required
            aria-describedby="news-excerpt-error"
        ></textarea>

        <div id="news-excerpt-error"></div>
    </div>

    <div>
        <label for="news-content">Матни хабар *</label>

        <textarea
            id="news-content"
            name="content"
            required
            aria-describedby="news-content-error"
        ></textarea>

        <div id="news-content-error"></div>
    </div>

    <button type="submit">Санҷидани форма</button>

</form>
<!-- Кнопка для проверки модального окна -->
<button type="button" class="delete-news-btn" data-id="1">
    Нест кардани хабар
</button>

<!-- Модальное окно -->
<div id="delete-modal" hidden>
    <div class="modal-content">
        <h2>Тасдиқи несткунӣ</h2>

        <p>Оё шумо мехоҳед ин хабарро нест кунед?</p>

        <button type="button" id="cancel-delete">
            Бекор кардан
        </button>

        <button type="button" id="confirm-delete">
            Нест кардан
        </button>
    </div>
</div>
(() => {
  const reveal = document.querySelector('[data-reveal-answer]');
  if (reveal) {
    reveal.addEventListener('click', () => {
      const answer = document.querySelector('[data-answer]');
      const grades = document.querySelector('[data-grades]');
      if (answer) answer.hidden = false;
      if (grades) grades.hidden = false;
      reveal.hidden = true;
    });
  }

  const hintCounter = document.querySelector('#hints-used');
  document.querySelectorAll('[data-hint]').forEach((hint) => {
    hint.addEventListener('toggle', () => {
      if (hint.open && hintCounter && !hint.dataset.counted) {
        hint.dataset.counted = '1';
        hintCounter.value = String(Number(hintCounter.value || 0) + 1);
      }
    });
  });

  const copy = document.querySelector('#copy-review');
  if (copy) {
    copy.addEventListener('click', async () => {
      const answer = document.querySelector('#answer');
      const text = [
        'Проверь моё самостоятельное решение задачи PHP Recall Lab.',
        '',
        'Тема: ' + copy.dataset.code,
        'Задание: ' + copy.dataset.title,
        'Условие: ' + copy.dataset.prompt,
        '',
        'Мой ответ:',
        answer ? answer.value : '',
        '',
        'Не давай готовое решение сразу. Сначала укажи конкретную ошибку и задай наводящий вопрос. В конце напиши один статус: «Принято» или «Нужно доработать».'
      ].join('\n');
      try {
        await navigator.clipboard.writeText(text);
        toast('Контекст скопирован. Вставьте его в десктопное приложение.');
      } catch (_) {
        if (answer) {
          answer.focus();
          answer.select();
        }
        toast('Буфер недоступен. Скопируйте ответ вручную.');
      }
    });
  }

  function toast(message) {
    const node = document.createElement('div');
    node.className = 'toast';
    node.textContent = message;
    document.body.appendChild(node);
    window.setTimeout(() => node.remove(), 3200);
  }
})();

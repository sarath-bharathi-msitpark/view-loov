@extends('company.layouts.company')

@section('page-title')
    {{ __('Break') }}
@endsection

@section('page-icon')
    {{ asset('assets/assestsnew/header-logo.svg') }}
@endsection

@push('css-page')
@endpush

@push('theme-script')
@endpush
@push('script-page')
    <script>
        document.getElementById('startDateCheck').addEventListener('change', function () {
            document.getElementById('startDateInput').disabled = !this.checked;
        });
    </script>

    <script>
        function findCommentBox(button) {
            // Traverse upwards to find the .comment-box from outside
            let parent = button.parentElement;
            while (parent && !parent.previousElementSibling?.classList?.contains('comment-box')) {
                parent = parent.parentElement;
            }
            return parent?.previousElementSibling || null;
        }

        // Reply toggle
        document.querySelectorAll('.reply-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const box = findCommentBox(btn);
                const reply = box?.querySelector('.reply-input');
                if (reply) reply.classList.toggle('d-none');
            });
        });

        // Edit toggle
        document.querySelectorAll('.edit-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const box = findCommentBox(btn);
                const edit = box?.querySelector('.edit-input');
                if (edit) edit.classList.toggle('d-none');
            });
        });

        // Delete
        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                if (confirm("Delete this comment?")) {
                    const box = findCommentBox(btn);
                    if (box) {
                        box.remove(); // remove comment
                        btn.closest('.d-flex').remove(); // remove edit/delete buttons
                    }
                }
            });
        });
    </script>


    <script>
        document.querySelector('.emoji-icon').addEventListener('click', () => {
            alert('Open emoji picker or image insert tool');
        });

        document.querySelector('.mention-icon').addEventListener('click', () => {
            alert('Show mention dropdown or autocomplete');
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Global variables
            let draggedCard = null;
            let activeDropArea = null;
            let cardBeingEdited = null;

            // Initialize the board
            initBoard();

            function initBoard() {
                // Initialize drag and drop for all cards
                initDragAndDrop();

                // Initialize add card buttons
                initAddCardButtons();

                // Initialize card menu dropdowns
                initCardMenus();

                // Initialize list add functionality
                initListAdder();

                // Initialize priority selectors
                initPrioritySelectors();
            }

            function initDragAndDrop() {
                const cards = document.querySelectorAll('.drag_card');
                const dropAreas = document.querySelectorAll('.drop-area');
                const listContents = document.querySelectorAll('.list-content');

                // Add event listeners to cards
                cards.forEach(card => {
                    card.addEventListener('dragstart', handleDragStart);
                    card.addEventListener('dragend', handleDragEnd);
                });

                // Add event listeners to drop areas
                dropAreas.forEach(area => {
                    area.addEventListener('dragenter', handleDragEnter);
                    area.addEventListener('dragover', handleDragOver);
                    area.addEventListener('dragleave', handleDragLeave);
                    area.addEventListener('drop', handleDrop);
                });

                // Add event listeners to list contents (for dropping directly into empty lists)
                listContents.forEach(list => {
                    list.addEventListener('dragover', handleListDragOver);
                    list.addEventListener('drop', handleListDrop);
                });
            }

            function handleDragStart(e) {
                draggedCard = this;
                this.classList.add('dragging');

                // Set data transfer for Firefox compatibility
                e.dataTransfer.setData('text/plain', this.dataset.cardId);
                e.dataTransfer.effectAllowed = 'move';

                // Hide dropdown if open
                const dropdown = this.querySelector('.card-dropdown');
                if (dropdown) {
                    dropdown.classList.remove('active');
                }
            }

            function handleDragEnd(e) {
                this.classList.remove('dragging');
                draggedCard = null;

                // Reset all drop areas
                document.querySelectorAll('.drop-area').forEach(area => {
                    area.classList.remove('active');
                });
            }

            function handleDragEnter(e) {
                e.preventDefault();
                if (draggedCard) {
                    this.classList.add('active');
                    activeDropArea = this;
                }
            }

            function handleDragOver(e) {
                e.preventDefault();
                e.dataTransfer.dropEffect = 'move';
            }

            function handleDragLeave(e) {
                this.classList.remove('active');
                if (activeDropArea === this) {
                    activeDropArea = null;
                }
            }

            function handleDrop(e) {
                e.preventDefault();
                if (!draggedCard) return;

                this.classList.remove('active');

                const listContent = this.closest('.list-content');
                const targetPosition = parseInt(this.dataset.position);

                // If dropping in the same list, we need to adjust the position
                const sameList = draggedCard.parentNode === listContent;
                const currentPosition = Array.from(listContent.children).indexOf(draggedCard) / 2; // Divide by 2 because of drop areas

                if (sameList && (targetPosition === currentPosition || targetPosition === currentPosition + 1)) {
                    return; // No change needed
                }

                // Move the card to the new position
                listContent.insertBefore(draggedCard, this);

                // Update the card's list reference if moving between lists
                updateCardAttributes(draggedCard, listContent.dataset.listId);

                // Renumber positions in affected lists
                updatePositions();

                saveCardPositions(); // Save the new order to storage
            }

            function handleListDragOver(e) {
                e.preventDefault();
                e.dataTransfer.dropEffect = 'move';
            }

            function handleListDrop(e) {
                e.preventDefault();
                if (!draggedCard) return;

                // Get the last drop area in the list
                const lastDropArea = this.querySelector('.drop-area:last-child');

                if (lastDropArea) {
                    // Simulate dropping on the last drop area
                    this.appendChild(draggedCard);

                    // Update the card's list reference
                    updateCardAttributes(draggedCard, this.dataset.listId);

                    // Renumber positions
                    updatePositions();

                    saveCardPositions(); // Save the new order to storage
                }
            }

            function updateCardAttributes(card, listId) {
                // Update data attributes or any other properties if needed
                card.dataset.listId = listId;
            }

            function updatePositions() {
                // Update all drop areas' position attributes
                document.querySelectorAll('.list-content').forEach(list => {
                    const items = Array.from(list.children);
                    items.filter(item => item.classList.contains('drop-area')).forEach((area, index) => {
                        area.dataset.position = index;
                    });
                });
            }

            function saveCardPositions() {
                // Implement logic to save card positions to localStorage or server
                // This is where you'd typically make an API call to save the new order
                const lists = document.querySelectorAll('.list');
                const boardData = Array.from(lists).map(list => {
                    const listId = list.dataset.listId;
                    const cards = Array.from(list.querySelectorAll('.drag_card')).map(card => ({
                        id: card.dataset.cardId,
                        title: card.querySelector('.card-title').textContent,
                        priority: getPriorityFromCard(card),
                        // Add any other card data you want to save
                    }));

                    return {
                        id: listId,
                        title: list.querySelector('.list-header-title').textContent,
                        cards: cards
                    };
                });

                // Save to localStorage for demo purposes
                localStorage.setItem('trelloBoardData', JSON.stringify(boardData));

                // In a real app, you'd send this to your backend:
                // fetch('/api/board/update', {
                //     method: 'POST',
                //     headers: { 'Content-Type': 'application/json' },
                //     body: JSON.stringify(boardData)
                // });
            }

            function getPriorityFromCard(card) {
                const priorityTag = card.querySelector('.priority-tag');
                if (priorityTag) {
                    if (priorityTag.classList.contains('priority-high')) return 'high';
                    if (priorityTag.classList.contains('priority-medium')) return 'medium';
                    if (priorityTag.classList.contains('priority-low')) return 'low';
                    if (priorityTag.classList.contains('priority-critical')) return 'critical';
                }
                return 'medium'; // Default priority
            }

            function initAddCardButtons() {
                // Add event listeners to "Add card" buttons
                const addCardButtons = document.querySelectorAll('.add-card-btn');
                const composers = document.querySelectorAll('.card-composer');
                const addButtons = document.querySelectorAll('.card-composer .add-btn');
                const cancelButtons = document.querySelectorAll('.card-composer .cancel-btn');

                addCardButtons.forEach((button, index) => {
                    button.addEventListener('click', function () {
                        // Hide all composers first
                        composers.forEach(composer => composer.classList.remove('active'));

                        // Show the clicked list's composer
                        composers[index].classList.add('active');

                        // Hide the add card button
                        this.style.display = 'none';

                        // Focus the textarea
                        composers[index].querySelector('textarea').focus();
                    });
                });

                addButtons.forEach((button, index) => {
                    button.addEventListener('click', function () {
                        const composer = this.closest('.card-composer');
                        const list = this.closest('.list');
                        const listContent = list.querySelector('.list-content');
                        const textarea = composer.querySelector('textarea');
                        const cardTitle = textarea.value.trim();

                        if (cardTitle) {
                            // Create a new card
                            const newCard = createNewCard(cardTitle, getSelectedPriority(composer));

                            // Add it to the end of the list
                            const lastDropArea = listContent.querySelector('.drop-area:last-child');
                            listContent.insertBefore(newCard, lastDropArea.nextSibling);

                            // Add drop area after the new card
                            const newDropArea = document.createElement('div');
                            newDropArea.className = 'drop-area';
                            newDropArea.dataset.position = listContent.querySelectorAll('.drop-area').length;
                            listContent.appendChild(newDropArea);
                            initCardDropArea(newDropArea);

                            // Initialize drag and drop for the new card
                            initCardDragAndDrop(newCard);

                            // Initialize the card menu
                            initCardMenu(newCard);

                            // Update positions
                            updatePositions();

                            // Save to storage
                            saveCardPositions();

                            // Reset and hide the composer
                            textarea.value = '';
                            composer.classList.remove('active');
                            addCardButtons[index].style.display = 'block';
                        }
                    });
                });

                cancelButtons.forEach((button, index) => {
                    button.addEventListener('click', function () {
                        // Hide the composer
                        composers[index].classList.remove('active');
                        // Show the add card button
                        addCardButtons[index].style.display = 'block';
                        // Clear the textarea
                        composers[index].querySelector('textarea').value = '';
                    });
                });
            }

            function createNewCard(title, priority = 'medium') {
                const card = document.createElement('div');
                card.className = 'card';
                card.draggable = true;
                card.dataset.cardId = Date.now().toString(); // Use timestamp for unique ID

                // HTML structure for the card
                card.innerHTML = `
            <div class="card-menu">⋮</div>
            <div class="card-dropdown">
                <div class="dropdown-item">Edit card</div>
                <div class="dropdown-item delete">Delete card</div>
            </div>
            <div class="card-title">${title}</div>
            <div class="priority-tag priority-${priority}">${priority.charAt(0).toUpperCase() + priority.slice(1)}</div>
            <div class="progress-container">
                <div class="progress-bar" style="width: 0%"></div>
            </div>
            <div class="card-meta">
                <div class="card-meta-left">
                   <div class="meta-item">
                                            <span><i class="fa-solid fa-paperclip fs-5"></i></span>
                                            <span>0</span>
                                        </div>
                                        <div class="meta-item">
                                            <span><i class="fa-regular fa-message fs-5"></i></span>
                                            <span>0</span>
                                        </div>
                </div>
            </div>



            <div class="row align-items-end">
            <div class="avatar-group col-6">
                <div class="avatar" style="background-image: url('data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 100 100\'%3E%3Ccircle cx=\'50\' cy=\'50\' r=\'50\' fill=\'%23e91e63\'/%3E%3Ctext x=\'50\' y=\'60\' font-size=\'30\' text-anchor=\'middle\' fill=\'white\'%3EA%3C/text%3E%3C/svg%3E');"></div>
            </div>
            <div class="date col-6 text-end">${formatDate(new Date())}</div>
            </div>
        `;

                return card;
            }

            function formatDate(date) {
                const day = String(date.getDate()).padStart(2, '0');
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const year = date.getFullYear();
                return `${day} ${getMonthName(date.getMonth())} ${year}`;
            }

            function getMonthName(month) {
                const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                return months[month];
            }

            function initCardDragAndDrop(card) {
                card.addEventListener('dragstart', handleDragStart);
                card.addEventListener('dragend', handleDragEnd);
            }

            function initCardDropArea(area) {
                area.addEventListener('dragenter', handleDragEnter);
                area.addEventListener('dragover', handleDragOver);
                area.addEventListener('dragleave', handleDragLeave);
                area.addEventListener('drop', handleDrop);
            }

            function initCardMenus() {
                // Set up all card menu toggles
                document.querySelectorAll('.card-menu').forEach(menu => {
                    menu.addEventListener('click', toggleCardMenu);
                });

                // Set up edit and delete buttons
                document.querySelectorAll('.dropdown-item').forEach(item => {
                    if (item.classList.contains('delete')) {
                        item.addEventListener('click', deleteCard);
                    } else {
                        item.addEventListener('click', editCard);
                    }
                });

                // Close dropdown when clicking outside
                document.addEventListener('click', function (e) {
                    if (!e.target.closest('.card-menu') && !e.target.closest('.card-dropdown')) {
                        document.querySelectorAll('.card-dropdown').forEach(dropdown => {
                            dropdown.classList.remove('active');
                        });
                    }
                });
            }

            function toggleCardMenu(e) {
                e.stopPropagation();

                // Close all other dropdowns first
                document.querySelectorAll('.card-dropdown').forEach(dropdown => {
                    if (dropdown !== this.nextElementSibling) {
                        dropdown.classList.remove('active');
                    }
                });

                // Toggle this dropdown
                const dropdown = this.nextElementSibling;
                dropdown.classList.toggle('active');
            }

            function deleteCard(e) {
                e.stopPropagation();

                const card = this.closest('.drag_card');

                // Ask for confirmation
                if (confirm('Are you sure you want to delete this card?')) {
                    // Remove the card
                    card.remove();

                    // Update positions
                    updatePositions();

                    // Save to storage
                    saveCardPositions();
                }

                // Close the dropdown
                this.closest('.card-dropdown').classList.remove('active');
            }

            function editCard(e) {
                e.stopPropagation();

                const card = this.closest('.drag_card');
                const cardTitle = card.querySelector('.card-title');
                const priority = getPriorityFromCard(card);

                // Store the original title
                const originalTitle = cardTitle.textContent;

                // Create an inline editor
                const editor = document.createElement('div');
                editor.className = 'card-editor';
                editor.innerHTML = `
            <textarea class="edit-title">${originalTitle}</textarea>
            <div class="priority-select">
                <button class="priority-option priority-low ${priority === 'low' ? 'active' : ''}" data-priority="low">Low</button>
                <button class="priority-option priority-medium ${priority === 'medium' ? 'active' : ''}" data-priority="medium">Medium</button>
                <button class="priority-option priority-high ${priority === 'high' ? 'active' : ''}" data-priority="high">High</button>
                <button class="priority-option priority-critical ${priority === 'critical' ? 'active' : ''}" data-priority="critical">Critical</button>
            </div>
            <div class="composer-controls">
                <button class="add-btn">Save</button>
                <button class="cancel-btn">Cancel</button>
            </div>
        `;

                // Hide the title and add the editor
                cardTitle.style.display = 'none';
                card.insertBefore(editor, cardTitle.nextSibling);

                // Focus the textarea
                const textarea = editor.querySelector('.edit-title');
                textarea.focus();
                textarea.setSelectionRange(textarea.value.length, textarea.value.length);

                // Set up priority selectors
                editor.querySelectorAll('.priority-option').forEach(option => {
                    option.addEventListener('click', function () {
                        editor.querySelectorAll('.priority-option').forEach(opt => {
                            opt.classList.remove('active');
                        });
                        this.classList.add('active');
                    });
                });

                // Set up save button
                editor.querySelector('.add-btn').addEventListener('click', function () {
                    const newTitle = textarea.value.trim();
                    if (newTitle) {
                        cardTitle.textContent = newTitle;

                        // Update priority
                        const newPriority = editor.querySelector('.priority-option.active').dataset.priority;
                        const priorityTag = card.querySelector('.priority-tag');
                        priorityTag.className = `priority-tag priority-${newPriority}`;
                        priorityTag.textContent = newPriority.charAt(0).toUpperCase() + newPriority.slice(1);

                        // Save changes
                        saveCardPositions();
                    }

                    // Remove editor and show title
                    editor.remove();
                    cardTitle.style.display = '';
                });

                // Set up cancel button
                editor.querySelector('.cancel-btn').addEventListener('click', function () {
                    // Remove editor and show title without changes
                    editor.remove();
                    cardTitle.style.display = '';
                });

                // Close the dropdown
                this.closest('.card-dropdown').classList.remove('active');

                // Store reference to the card being edited
                cardBeingEdited = card;
            }

            function getSelectedPriority(composer) {
                const activeOption = composer.querySelector('.priority-option.active');
                return activeOption ? activeOption.dataset.priority : 'medium';
            }

            function initPrioritySelectors() {
                document.querySelectorAll('.priority-select .priority-option').forEach(option => {
                    option.addEventListener('click', function () {
                        // Remove active class from all options in this selector
                        const prioritySelect = this.closest('.priority-select');
                        prioritySelect.querySelectorAll('.priority-option').forEach(opt => {
                            opt.classList.remove('active');
                        });

                        // Add active class to clicked option
                        this.classList.add('active');
                    });
                });
            }

            function initListAdder() {
                // This would be implemented to add new lists to the board
                // For now, we'll leave this as a placeholder
                /*
                const addListBtn = document.querySelector('.list-add-btn');
                const listComposer = document.querySelector('.list-composer');

                if (addListBtn && listComposer) {
                    addListBtn.addEventListener('click', function() {
                        this.closest('.list-add-container').style.display = 'none';
                        listComposer.classList.add('active');
                        listComposer.querySelector('input').focus();
                    });

                    const addBtn = listComposer.querySelector('.add-btn');
                    const cancelBtn = listComposer.querySelector('.cancel-btn');

                    addBtn.addEventListener('click', function() {
                        const title = listComposer.querySelector('input').value.trim();
                        if (title) {
                            // Create new list
                            const newList = createNewList(title);

                            // Add to board
                            const board = document.querySelector('.board');
                            board.insertBefore(newList, document.querySelector('.list-add-container'));

                            // Initialize the new list
                            initListFunctionality(newList);

                            // Reset and hide composer
                            listComposer.querySelector('input').value = '';
                            listComposer.classList.remove('active');
                            document.querySelector('.list-add-container').style.display = 'block';
                        }
                    });

                    cancelBtn.addEventListener('click', function() {
                        listComposer.classList.remove('active');
                        listComposer.querySelector('input').value = '';
                        document.querySelector('.list-add-container').style.display = 'block';
                    });
                }
                */
            }

            // Call this function when loading from saved data
            function loadBoardData() {
                const savedData = localStorage.getItem('trelloBoardData');
                if (savedData) {
                    const boardData = JSON.parse(savedData);

                    // Implementation would go here to render the saved board state
                }
            }

            // Optional: Try to load saved data on initialization
            // loadBoardData();

            // Optional: Auto-save board state periodically
            // setInterval(saveCardPositions, 30000); // Every 30 seconds
        });
    </script>

    <script>

        function openModalFromOffcanvas(modalId) {
            const offcanvasEl = document.querySelector('.offcanvas.show');
            const modalEl = document.querySelector(modalId);

            // Hide the offcanvas
            if (offcanvasEl) {
                const offcanvas = bootstrap.Offcanvas.getInstance(offcanvasEl);
                offcanvas.hide();

                // Wait for the offcanvas to hide before showing modal
                offcanvasEl.addEventListener('hidden.bs.offcanvas', function handler() {
                    offcanvasEl.removeEventListener('hidden.bs.offcanvas', handler);
                    const modal = new bootstrap.Modal(modalEl);
                    modal.show();
                });
            } else {
                const modal = new bootstrap.Modal(modalEl);
                modal.show();
            }
        }
    </script>

    <script>
        const modal = bootstrap.Modal.getInstance(document.getElementById('myModal'));
        modal.hide();
    </script>

    <script>
        function toggleStartDate(checkbox) {
            const calendarContainer = document.getElementById('calendarContainer');

            if (checkbox.checked) {
                renderCalendar(calendarContainer);
            } else {
                calendarContainer.innerHTML = '';
            }
        }

        function renderCalendar(container) {
            // Get current date
            const currentDate = new Date();
            const currentMonth = currentDate.getMonth();
            const currentYear = currentDate.getFullYear();

            // Create calendar HTML
            const calendarHTML = createCalendarHTML(currentMonth, currentYear);

            // Insert calendar into container
            container.innerHTML = calendarHTML;

            // Add event listeners to date cells
            addDateEventListeners();

            // Add event listeners to navigation buttons
            document.querySelector('.prev-month-btn').addEventListener('click', () => {
                navigateMonth(-1, container);
            });

            document.querySelector('.next-month-btn').addEventListener('click', () => {
                navigateMonth(1, container);
            });
        }

        function createCalendarHTML(month, year) {
            const monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
                'July', 'August', 'September', 'October', 'November', 'December'];

            const firstDay = new Date(year, month, 1).getDay(); // 0 = Sunday
            const daysInMonth = new Date(year, month + 1, 0).getDate();
            const lastMonthDays = new Date(year, month, 0).getDate();

            // Adjust for Monday as first day of week
            const startDay = firstDay === 0 ? 6 : firstDay - 1;

            let calendarHTML = `
                <div class="calendar-container" style="display: block;">
                    <div class="calendar-header">
                        <button class="nav-btn prev-month-btn">&lt;</button>
                        <div class="month-year">
                            <div class="dropdown">
                                <div class="month" id="currentMonth">${monthNames[month]}</div>
                                <div class="dropdown-content" id="monthDropdown">
                                    ${monthNames.map((m, i) => `<div class="dropdown-item" data-month="${i}">${m}</div>`).join('')}
                                </div>
                            </div>
                            <div class="dropdown">
                                <div class="year" id="currentYear">${year}</div>
                                <div class="dropdown-content" id="yearDropdown">
                                    ${generateYearOptions(year)}
                                </div>
                            </div>
                        </div>
                        <button class="nav-btn next-month-btn">&gt;</button>
                    </div>
                    <div class="calendar-grid">
                        <div class="day-header">Mo</div>
                        <div class="day-header">Tu</div>
                        <div class="day-header">We</div>
                        <div class="day-header">Th</div>
                        <div class="day-header">Fr</div>
                        <div class="day-header">Sa</div>
                        <div class="day-header">Su</div>
            `;

            // Previous month days
            for (let i = 0; i < startDay; i++) {
                const prevMonthDay = lastMonthDays - startDay + i + 1;
                calendarHTML += `<div class="day prev-month">${prevMonthDay}</div>`;
            }

            // Current month days
            const today = new Date();
            const isCurrentMonth = today.getMonth() === month && today.getFullYear() === year;
            const todayDate = today.getDate();

            for (let day = 1; day <= daysInMonth; day++) {
                const isToday = isCurrentMonth && day === todayDate;
                calendarHTML += `<div class="day ${isToday ? 'today' : ''}" data-date="${year}-${month + 1}-${day}">${day}</div>`;
            }

            // Next month days
            const totalCells = 42; // 6 rows × 7 days
            const nextMonthDays = totalCells - daysInMonth - startDay;

            for (let i = 1; i <= nextMonthDays; i++) {
                calendarHTML += `<div class="day next-month">${i}</div>`;
            }

            calendarHTML += `
                    </div>
                </div>
            `;

            return calendarHTML;
        }

        function addDateEventListeners() {
            const days = document.querySelectorAll('.day:not(.prev-month):not(.next-month)');

            days.forEach(day => {
                day.addEventListener('click', () => {
                    // Remove selected class from all days
                    document.querySelectorAll('.day').forEach(d => {
                        d.classList.remove('selected');
                    });

                    // Add selected class to clicked day
                    day.classList.add('selected');

                    // You can trigger an event or save the selected date here
                    const selectedDate = day.getAttribute('data-date');
                    console.log('Selected date:', selectedDate);
                });
            });

            // Add month dropdown toggle
            const monthElement = document.getElementById('currentMonth');
            const monthDropdown = document.getElementById('monthDropdown');

            monthElement.addEventListener('click', () => {
                closeAllDropdowns();
                monthDropdown.classList.toggle('show');
            });

            // Add year dropdown toggle
            const yearElement = document.getElementById('currentYear');
            const yearDropdown = document.getElementById('yearDropdown');

            yearElement.addEventListener('click', () => {
                closeAllDropdowns();
                yearDropdown.classList.toggle('show');
            });

            // Add month selection functionality
            const monthItems = document.querySelectorAll('#monthDropdown .dropdown-item');
            monthItems.forEach(item => {
                item.addEventListener('click', () => {
                    const selectedMonth = parseInt(item.getAttribute('data-month'));
                    const currentYear = parseInt(document.getElementById('currentYear').textContent);
                    updateCalendar(selectedMonth, currentYear);
                    monthDropdown.classList.remove('show');
                });
            });

            // Add year selection functionality
            const yearItems = document.querySelectorAll('#yearDropdown .dropdown-item');
            yearItems.forEach(item => {
                item.addEventListener('click', () => {
                    const selectedYear = parseInt(item.getAttribute('data-year'));
                    const currentMonth = document.getElementById('currentMonth').textContent;
                    const monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
                        'July', 'August', 'September', 'October', 'November', 'December'];
                    const selectedMonth = monthNames.indexOf(currentMonth);
                    updateCalendar(selectedMonth, selectedYear);
                    yearDropdown.classList.remove('show');
                });
            });

            // Close dropdowns when clicking outside
            document.addEventListener('click', (e) => {
                if (!e.target.matches('.month') && !e.target.matches('.year') &&
                    !e.target.matches('.dropdown-item')) {
                    closeAllDropdowns();
                }
            });
        }

        function closeAllDropdowns() {
            const dropdowns = document.querySelectorAll('.dropdown-content');
            dropdowns.forEach(dropdown => {
                dropdown.classList.remove('show');
            });
        }

        function updateCalendar(month, year) {
            const container = document.getElementById('calendarContainer');
            const calendarHTML = createCalendarHTML(month, year);
            container.innerHTML = calendarHTML;

            addDateEventListeners();

            document.querySelector('.prev-month-btn').addEventListener('click', () => {
                navigateMonth(-1, container);
            });

            document.querySelector('.next-month-btn').addEventListener('click', () => {
                navigateMonth(1, container);
            });
        }

        function generateYearOptions(currentYear) {
            let yearsHTML = '';
            // Generate a range of years (current year ± 10 years)
            const startYear = currentYear - 10;
            const endYear = currentYear + 10;

            for (let year = startYear; year <= endYear; year++) {
                yearsHTML += `<div class="dropdown-item" data-year="${year}">${year}</div>`;
            }

            return yearsHTML;
        }

        function navigateMonth(direction, container) {
            const monthElement = document.querySelector('#currentMonth');
            const yearElement = document.querySelector('#currentYear');

            const monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
                'July', 'August', 'September', 'October', 'November', 'December'];

            let month = monthNames.indexOf(monthElement.textContent);
            let year = parseInt(yearElement.textContent);

            month += direction;

            if (month < 0) {
                month = 11;
                year--;
            } else if (month > 11) {
                month = 0;
                year++;
            }

            updateCalendar(month, year);
        }
    </script>


    <script>
        document.getElementById('openOffcanvas').addEventListener('click', function (e) {
            const offcanvasEl = document.getElementById('offcanvasRight');
            const offcanvas = bootstrap.Offcanvas.getOrCreateInstance(offcanvasEl);
            offcanvas.show();
        });
    </script>

@endpush

@section('content')
    @include('company.layouts.partials.nav')
    <div class="col-12">
        <div class="row mt-5">
            <div class="col-lg-6 selecters_head">
                <h2 class="mb-0" id="sectionHeading">Newsletter Templates's Tasks</h2>
            </div>

            <div class="col-lg-6 selecters_head">
                <div class="row justify-content-lg-end gx-4">
                    <div class="col-auto">
                                    <span>
                                        <a class="download_arrbtn d-flex align-items-center justify-content-center"
                                           href="{{ route('organization.task_management.taskStage') }}">
                                            <img src="{{ asset('assets/assestsnew/editblue.svg') }}" alt="">
                                        </a>
                                    </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Task Template -->
    <div class="board " style="background: #EBF4FF;">
        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRight"
             aria-labelledby="offcanvasRightLabel">
            <div class="offcanvas-header">
                <div class="d-flex w-100">
                    <div class="col-6">
                        <h5>Task Details</h5>
                    </div>
                    <div class="col-6">
                        <div class="d-flex justify-content-end align-items-center gap-3">
                            <button type="button" class="trash_ofsetbtn"><i
                                    class="fa-solid fa-trash-can"></i></button>
                            <button type="button" class="share_btnoffset"><i
                                    class="fa-solid fa-share-nodes"></i></button>
                            <button type="button" class="btn-close text-reset p-0 m-0"
                                    data-bs-dismiss="offcanvas" aria-label="Close"></button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="offcanvas-body">
                <div class="d-flex flex-column">
                    <div class="col-12">
                        <div class="row">
                            <div class="col-12 gap-3 bottom_deviders">
                                <h4 class="d-flex align-items-center">Build Website Design for Client 025
                                    <div class="priority-tag priority-medium mx-2 mb-0">Medium</div>
                                </h4>
                                <span class="in_listerspan_select">in list
                                                <select class="px-1 py-1">
                                                    <option>To do</option>
                                                    <option>To do1</option>
                                                    <option>To do2</option>
                                                </select>
                                            </span>
                            </div>
                            <div class="col-12 gap-3 bottom_deviders mt-3">
                                <h4 class="d-flex align-items-center"><i
                                        class="fa-regular fa-user me-3"></i>Members</h4>
                                <div class="my-2 px-3 sugges_image">
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="{{ asset('assets/assestsnew/roundcli1.svg') }}" alt="">
                                        <img src="{{ asset('assets/assestsnew/roundcli2.svg') }}" alt="">
                                        <button class="add_roundbtnblue" type="button"
                                                onclick="openModalFromOffcanvas('#myModal')">
                                            <i class="fa-solid fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 gap-3 bottom_deviders mt-3">
                                <div class="d-flex justify-content-between mb-3 align-items-center">
                                    <h4 class="d-flex align-items-center"><i
                                            class="fa-regular fa-newspaper me-3"></i>Description</h4>
                                    <button class="edit_blues">Edit<i
                                            class="fa-solid fa-pen-to-square ms-2"></i></button>
                                </div>
                                <span style="color: #6D6D6D;">
                                                Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod
                                                tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim
                                                veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea
                                                commodo consequat. Duis aute irure dolor in reprehenderit in voluptate
                                                velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint
                                                occaecat cupidatat non proident, sunt in culpa qui officia deserunt
                                                mollit anim id est laborum.
                                            </span>
                            </div>
                            <div class="col-12 gap-3 bottom_deviders mt-3">
                                <div class="d-flex justify-content-between mb-3 align-items-center">
                                    <h4 class="d-flex align-items-center"><i
                                            class="fa-solid fa-paperclip me-3"></i>Attachments</h4>
                                    <button class="edit_blues">Add<i
                                            class="fa-solid fa-plus ms-2"></i></button>
                                </div>
                                <div class="d-flex align-items-center mb-3" style="position: relative;">
                                    <div>
                                        <div class="row pe-3 justify-content-center align-items-center">
                                            <img style="width: 80px;"
                                                 src="{{ asset('assets/assestsnew/documents.svg') }}" alt="">
                                        </div>
                                    </div>
                                    <div class="col-9 main_documentnames">
                                        <div class="row">
                                            <h6>Design Requirements Reports</h6>
                                            <small>Upload 25 Apr 2025, 10:00am</small>
                                        </div>
                                    </div>
                                    <div class="card-menu">⋮</div>
                                    <div class="card-dropdown">
                                        <div class="dropdown-item">Edit card</div>
                                        <div class="dropdown-item delete">Delete card</div>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center mb-3" style="position: relative;">
                                    <div>
                                        <div class="row pe-3 justify-content-center align-items-center">
                                            <img style="width: 80px;"
                                                 src="{{ asset('assets/assestsnew/documents.svg') }}" alt="">
                                        </div>
                                    </div>
                                    <div class="col-9 main_documentnames">
                                        <div class="row">
                                            <h6>Design Requirements Reports</h6>
                                            <small>Upload 25 Apr 2025, 10:00am</small>
                                        </div>
                                    </div>
                                    <div class="card-menu">⋮</div>
                                    <div class="card-dropdown">
                                        <div class="dropdown-item">Edit card</div>
                                        <div class="dropdown-item delete">Delete card</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 gap-3 bottom_deviders mt-3">
                                <div class="d-flex justify-content-between mb-3 align-items-center">
                                    <h4 class="d-flex align-items-center"><i
                                            class="fas fa-sliders-h me-3"></i></i>Custom Fields</h4>
                                    <button class="edit_blues" onclick="openModalFromOffcanvas('#selectMembersModal')">
                                        Add <i class="fa-solid fa-plus ms-2"></i>
                                    </button>
                                </div>
                                <div class="row">
                                    <div class="col-4">

                                        <small class="fw-medium fs-6">Status</small>
                                        <select class="form-select mt-1" style="background-color: #EAF5FF;">
                                            <option value="">High</option>
                                            <option value="">Team 1</option>
                                            <option value="">Team 2</option>
                                            <option value="">Team 3</option>
                                        </select>
                                    </div>
                                    <div class="col-4">

                                        <small class="fw-medium fs-6">T-Shirt Size</small>
                                        <select class="form-select mt-1" style="background-color: #EAF5FF;">
                                            <option value="">L-32 hrs</option>
                                            <option value="">Team 1</option>
                                            <option value="">Team 2</option>
                                            <option value="">Team 3</option>
                                        </select>
                                    </div>
                                    <div class="col-4">

                                        <small class="fw-medium fs-6">Actual Time</small>
                                        <select class="form-select mt-1">
                                            <option value="">25 hrs</option>
                                            <option value="">Team 1</option>
                                            <option value="">Team 2</option>
                                            <option value="">Team 3</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 gap-3 bottom_deviders mt-3">
                                <div class="d-flex justify-content-between mb-3 align-items-center">
                                    <h4 class="d-flex align-items-center"><i
                                            class="fa-solid fa-calendar me-3"></i></i>Dates</h4>
                                    <button class="edit_blues" onclick="openModalFromOffcanvas('#dateModal')">
                                        Edit <i class="fa-solid fa-pen-to-square ms-2"></i>
                                    </button>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <small class="fw-medium fs-6">Start Date</small>
                                        <p class="fw-medium fs-6" style="color: #A2A2A2;">26 Apr 2025</p>
                                    </div>
                                    <div class="col-md-4">
                                        <small class="fw-medium fs-6">End Date</small>
                                        <p class="fw-medium fs-6" style="color: #A2A2A2;">26 Apr 2025</p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 gap-3 bottom_deviders mt-3">
                                <div class="d-flex justify-content-between mb-3 align-items-center">
                                    <h4 class="d-flex align-items-center"><i
                                            class="fa-solid fa-message me-3"></i>Activity</h4>
                                </div>
                                <div class="comment-box d-flex align-items-center px-3 py-2">
                                    <label class="circle-icon bg-light text-primary m-0" for="fileUpload">
                                        <i class="fas fa-plus"></i>
                                    </label>
                                    <input type="file" id="fileUpload" class="d-none">

                                    <div class="d-flex align-items-center gap-2 ms-2">
                                        <i class="far fa-smile emoji-icon fs-4"
                                           style="cursor: pointer;"></i>
                                        <i class="fas fa-at mention-icon fs-4" style="cursor: pointer;"></i>

                                        <input type="text" class="comment-input flex-grow-1"
                                               placeholder="Add Comments">
                                    </div>

                                    <div class="ms-auto">
                                        <button class="send-btn">
                                            <i class="fas fa-paper-plane"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="comment-box d-flex align-items-center p-3 mb-2 mt-4">
                                    <img src="{{ asset('assets/assestsnew/woman1.png') }}" class="rounded-circle me-2"
                                         alt="avatar">
                                    <div class="flex-grow-1">
                                        <p class="mb-1"><strong>User Name</strong> <span
                                                class="text-muted">Lorem ipsum dolor sit amet,
                                                            consectetur adipiscing elit, sed do</span></p>
                                        <p class="text-primary mb-1" style="font-size: 14px;">28 Apr
                                            2025 19:55 pm</p>
                                        <div class="reply-input d-none mt-2">
                                                        <textarea class="form-control" rows="1"
                                                                  placeholder="Write a reply..."></textarea>
                                            <button class="btn btn-sm btn-primary mt-1">Send</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex gap-3">
                                    <a href="#"
                                       class="text-muted text-decoration-none small reply-btn">Reply</a>
                                </div>

                                <div class="reply-input d-none mt-2">
                                                <textarea class="form-control" rows="1"
                                                          placeholder="Write a reply..."></textarea>
                                    <button class="btn btn-sm btn-primary mt-1">Send</button>
                                </div>

                                <div class="comment-box d-flex align-items-center p-3 mb-2 mt-4">
                                    <img src="{{ asset('assets/assestsnew/woman2.png') }}" class="rounded-circle me-2"
                                         alt="avatar">
                                    <div class="flex-grow-1">
                                        <p class="mb-1">
                                            <strong>Admin</strong>
                                            <span class="badge bg-light text-primary border me-2"><i
                                                    class="fas fa-file-word me-1"></i>Reports</span>
                                            <span class="text-muted">Lorem ipsum dolor sit amet,
                                                            consectetur adipiscing elit, sed do</span>
                                        </p>
                                        <p class="text-primary mb-1" style="font-size: 14px;">30 Apr
                                            2025 10:55 pm</p>


                                        <div class="edit-input d-none mt-2">
                                                        <textarea class="form-control"
                                                                  rows="1">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do</textarea>
                                            <button class="btn btn-sm btn-success mt-1">Update</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex gap-3">
                                    <a href="#"
                                       class="text-decoration-none text-muted small edit-btn">Edit</a>
                                    <a href="#"
                                       class="text-decoration-none text-muted small delete-btn">Delete</a>
                                </div>


                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="list" data-list-id="1">
            <div class="list-header">
                <div class="list-header-title">To Do</div>
                <button class="add-button">+</button>
            </div>
            <div class="list-content" data-list-id="1">
                <div class="drop-area" data-position="0"></div>
                <div class="drag_card">
                    <div class="card-menu" id="openOffcanvas" data-card-id="1">⋮</div>
                    <div class="card-dropdown">
                        <div class="dropdown-item">Edit card</div>
                        <div class="dropdown-item delete">Delete card</div>
                    </div>
                    <div class="card-title">Create the app's wireframe</div>
                    <div class="priority-tag priority-medium">Medium</div>
                    <div class="progress-container">
                        <div class="progress-bar" style="width: 68%"></div>
                    </div>
                    <div class="card-meta">
                        <div class="card-meta-left">
                            <div class="meta-item">
                                <span><i class="fa-solid fa-paperclip fs-5"></i></span>
                                <span>2</span>
                            </div>
                            <div class="meta-item">
                                <span><i class="fa-regular fa-message fs-5"></i></span>
                                <span>2</span>
                            </div>
                        </div>
                    </div>
                    <div class="row align-items-end">
                        <div class="avatar-group col-6">
                            <div class="avatar"
                                 style="background-image: url('data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 100 100\'%3E%3Ccircle cx=\'50\' cy=\'50\' r=\'50\' fill=\'%23e91e63\'/%3E%3Ctext x=\'50\' y=\'60\' font-size=\'30\' text-anchor=\'middle\' fill=\'white\'%3EA%3C/text%3E%3C/svg%3E');">
                            </div>
                            <div class="avatar"
                                 style="background-image: url('data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 100 100\'%3E%3Ccircle cx=\'50\' cy=\'50\' r=\'50\' fill=\'%232196f3\'/%3E%3Ctext x=\'50\' y=\'60\' font-size=\'30\' text-anchor=\'middle\' fill=\'white\'%3EB%3C/text%3E%3C/svg%3E');">
                            </div>
                        </div>
                        <div class="date col-6 text-end">02 Jan 2025</div>
                    </div>
                </div>
                <div class="drop-area" data-position="1"></div>

                <div class="drag_card" draggable="true" data-card-id="2">
                    <div class="card-menu">⋮</div>
                    <div class="card-dropdown">
                        <div class="dropdown-item">Edit card</div>
                        <div class="dropdown-item delete">Delete card</div>
                    </div>
                    <div class="card-title">Create the app's wireframe</div>
                    <div class="priority-tag priority-low">Low</div>
                    <div class="progress-container">
                        <div class="progress-bar" style="width: 68%"></div>
                    </div>
                    <div class="card-meta">
                        <div class="card-meta-left">
                            <div class="meta-item">
                                <span><i class="fa-solid fa-paperclip fs-5"></i></span>
                                <span>2</span>
                            </div>
                            <div class="meta-item">
                                <span><i class="fa-regular fa-message fs-5"></i></span>
                                <span>2</span>
                            </div>
                        </div>
                    </div>
                    <div class="row align-items-end">
                        <div class="avatar-group col-6">
                            <div class="avatar"
                                 style="background-image: url('data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 100 100\'%3E%3Ccircle cx=\'50\' cy=\'50\' r=\'50\' fill=\'%23e91e63\'/%3E%3Ctext x=\'50\' y=\'60\' font-size=\'30\' text-anchor=\'middle\' fill=\'white\'%3EA%3C/text%3E%3C/svg%3E');">
                            </div>
                            <div class="avatar"
                                 style="background-image: url('data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 100 100\'%3E%3Ccircle cx=\'50\' cy=\'50\' r=\'50\' fill=\'%232196f3\'/%3E%3Ctext x=\'50\' y=\'60\' font-size=\'30\' text-anchor=\'middle\' fill=\'white\'%3EB%3C/text%3E%3C/svg%3E');">
                            </div>
                        </div>
                        <div class="date col-6 text-end">02 Jan 2025</div>
                    </div>
                </div>
                <div class="drop-area" data-position="2"></div>

                <div class="drag_card" draggable="true" data-card-id="3">
                    <div class="card-menu">⋮</div>
                    <div class="card-dropdown">
                        <div class="dropdown-item">Edit card</div>
                        <div class="dropdown-item delete">Delete card</div>
                    </div>
                    <div class="card-title">Create the app's wireframe</div>
                    <div class="priority-tag priority-high">High</div>
                    <div class="progress-container">
                        <div class="progress-bar" style="width: 68%"></div>
                    </div>
                    <div class="card-meta">
                        <div class="card-meta-left">
                            <div class="meta-item">
                                <span><i class="fa-solid fa-paperclip fs-5"></i></span>
                                <span>2</span>
                            </div>
                            <div class="meta-item">
                                <span><i class="fa-regular fa-message fs-5"></i></span>
                                <span>2</span>
                            </div>
                        </div>
                    </div>
                    <div class="row align-items-end">
                        <div class="avatar-group col-6">
                            <div class="avatar"
                                 style="background-image: url('data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 100 100\'%3E%3Ccircle cx=\'50\' cy=\'50\' r=\'50\' fill=\'%23e91e63\'/%3E%3Ctext x=\'50\' y=\'60\' font-size=\'30\' text-anchor=\'middle\' fill=\'white\'%3EA%3C/text%3E%3C/svg%3E');">
                            </div>
                            <div class="avatar"
                                 style="background-image: url('data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 100 100\'%3E%3Ccircle cx=\'50\' cy=\'50\' r=\'50\' fill=\'%232196f3\'/%3E%3Ctext x=\'50\' y=\'60\' font-size=\'30\' text-anchor=\'middle\' fill=\'white\'%3EB%3C/text%3E%3C/svg%3E');">
                            </div>
                        </div>
                        <div class="date col-6 text-end">02 Jan 2025</div>
                    </div>
                </div>
                <div class="drop-area" data-position="3"></div>
            </div>

            <div class="card-composer">
                <textarea placeholder="Enter card title..."></textarea>
                <div class="priority-select">
                    <button class="priority-option priority-low active" data-priority="low">Low</button>
                    <button class="priority-option priority-medium" data-priority="medium">Medium</button>
                    <button class="priority-option priority-high" data-priority="high">High</button>
                    <button class="priority-option priority-critical"
                            data-priority="critical">Critical
                    </button>
                </div>
                <div class="composer-controls">
                    <button class="add-btn">Add card</button>
                    <button class="cancel-btn">Cancel</button>
                </div>
            </div>
            <button class="add-card-btn">+ Add card</button>
        </div>
        <div class="list" data-list-id="2">
            <div class="list-header">
                <div class="list-header-title">In Progress</div>
                <button class="add-button">+</button>
            </div>
            <div class="list-content" data-list-id="2">
                <div class="drop-area" data-position="0"></div>
                <div class="drag_card" draggable="true" data-card-id="4">
                    <div class="card-menu">⋮</div>
                    <div class="card-dropdown">
                        <div class="dropdown-item">Edit card</div>
                        <div class="dropdown-item delete">Delete card</div>
                    </div>
                    <div class="card-title">I am testing</div>
                    <div class="priority-tag priority-high">High</div>
                    <div class="progress-container">
                        <div class="progress-bar" style="width: 68%"></div>
                    </div>
                    <div class="card-meta">
                        <div class="card-meta-left">
                            <div class="meta-item">
                                <span><i class="fa-solid fa-paperclip fs-5"></i></span>
                                <span>2</span>
                            </div>
                            <div class="meta-item">
                                <span><i class="fa-regular fa-message fs-5"></i></span>
                                <span>2</span>
                            </div>
                        </div>
                    </div>
                    <div class="row align-items-end">
                        <div class="avatar-group col-6">
                            <div class="avatar"
                                 style="background-image: url('data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 100 100\'%3E%3Ccircle cx=\'50\' cy=\'50\' r=\'50\' fill=\'%23e91e63\'/%3E%3Ctext x=\'50\' y=\'60\' font-size=\'30\' text-anchor=\'middle\' fill=\'white\'%3EA%3C/text%3E%3C/svg%3E');">
                            </div>
                            <div class="avatar"
                                 style="background-image: url('data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 100 100\'%3E%3Ccircle cx=\'50\' cy=\'50\' r=\'50\' fill=\'%232196f3\'/%3E%3Ctext x=\'50\' y=\'60\' font-size=\'30\' text-anchor=\'middle\' fill=\'white\'%3EB%3C/text%3E%3C/svg%3E');">
                            </div>
                        </div>
                        <div class="date col-6 text-end">02 Jan 2025</div>
                    </div>
                </div>
                <div class="drop-area" data-position="1"></div>

                <div class="drag_card" draggable="true" data-card-id="5">
                    <div class="card-menu">⋮</div>
                    <div class="card-dropdown">
                        <div class="dropdown-item">Edit card</div>
                        <div class="dropdown-item delete">Delete card</div>
                    </div>
                    <div class="card-title">Create the app's wireframe</div>
                    <div class="priority-tag priority-medium">Medium</div>
                    <div class="progress-container">
                        <div class="progress-bar" style="width: 68%"></div>
                    </div>
                    <div class="card-meta">
                        <div class="card-meta-left">
                            <div class="meta-item">
                                <span><i class="fa-solid fa-paperclip fs-5"></i></span>
                                <span>2</span>
                            </div>
                            <div class="meta-item">
                                <span><i class="fa-regular fa-message fs-5"></i></span>
                                <span>2</span>
                            </div>
                        </div>
                    </div>
                    <div class="row align-items-end">
                        <div class="avatar-group col-6">
                            <div class="avatar"
                                 style="background-image: url('data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 100 100\'%3E%3Ccircle cx=\'50\' cy=\'50\' r=\'50\' fill=\'%23e91e63\'/%3E%3Ctext x=\'50\' y=\'60\' font-size=\'30\' text-anchor=\'middle\' fill=\'white\'%3EA%3C/text%3E%3C/svg%3E');">
                            </div>
                            <div class="avatar"
                                 style="background-image: url('data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 100 100\'%3E%3Ccircle cx=\'50\' cy=\'50\' r=\'50\' fill=\'%232196f3\'/%3E%3Ctext x=\'50\' y=\'60\' font-size=\'30\' text-anchor=\'middle\' fill=\'white\'%3EB%3C/text%3E%3C/svg%3E');">
                            </div>
                        </div>
                        <div class="date col-6 text-end">02 Jan 2025</div>
                    </div>
                </div>
                <div class="drop-area" data-position="2"></div>

                <div class="drag_card" draggable="true" data-card-id="6">
                    <div class="card-menu">⋮</div>
                    <div class="card-dropdown">
                        <div class="dropdown-item">Edit card</div>
                        <div class="dropdown-item delete">Delete card</div>
                    </div>
                    <div class="card-title">Create the app's wireframe</div>
                    <div class="priority-tag priority-low">Low</div>
                    <div class="progress-container">
                        <div class="progress-bar" style="width: 68%"></div>
                    </div>
                    <div class="card-meta">
                        <div class="card-meta-left">
                            <div class="meta-item">
                                <span><i class="fa-solid fa-paperclip fs-5"></i></span>
                                <span>2</span>
                            </div>
                            <div class="meta-item">
                                <span><i class="fa-regular fa-message fs-5"></i></span>
                                <span>2</span>
                            </div>
                        </div>
                    </div>
                    <div class="row align-items-end">
                        <div class="avatar-group col-6">
                            <div class="avatar"
                                 style="background-image: url('data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 100 100\'%3E%3Ccircle cx=\'50\' cy=\'50\' r=\'50\' fill=\'%23e91e63\'/%3E%3Ctext x=\'50\' y=\'60\' font-size=\'30\' text-anchor=\'middle\' fill=\'white\'%3EA%3C/text%3E%3C/svg%3E');">
                            </div>
                            <div class="avatar"
                                 style="background-image: url('data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 100 100\'%3E%3Ccircle cx=\'50\' cy=\'50\' r=\'50\' fill=\'%232196f3\'/%3E%3Ctext x=\'50\' y=\'60\' font-size=\'30\' text-anchor=\'middle\' fill=\'white\'%3EB%3C/text%3E%3C/svg%3E');">
                            </div>
                        </div>
                        <div class="date col-6 text-end">02 Jan 2025</div>
                    </div>
                </div>
                <div class="drop-area" data-position="3"></div>
            </div>
        </div>
        <div class="list" data-list-id="3">
            <div class="list-header">
                <div class="list-header-title">Review</div>
                <button class="add-button">+</button>
            </div>
            <div class="list-content" data-list-id="2">
                <div class="drop-area" data-position="0"></div>
                <div class="drag_card" draggable="true" data-card-id="4">
                    <div class="card-menu">⋮</div>
                    <div class="card-dropdown">
                        <div class="dropdown-item">Edit card</div>
                        <div class="dropdown-item delete">Delete card</div>
                    </div>
                    <div class="card-title">Application wireframe</div>
                    <div class="priority-tag priority-low">Low</div>
                    <div class="progress-container">
                        <div class="progress-bar" style="width: 68%"></div>
                    </div>
                    <div class="card-meta">
                        <div class="card-meta-left">
                            <div class="meta-item">
                                <span><i class="fa-solid fa-paperclip fs-5"></i></span>
                                <span>2</span>
                            </div>
                            <div class="meta-item">
                                <span><i class="fa-regular fa-message fs-5"></i></span>
                                <span>2</span>
                            </div>
                        </div>
                    </div>
                    <div class="row align-items-end">
                        <div class="avatar-group col-6">
                            <div class="avatar"
                                 style="background-image: url('data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 100 100\'%3E%3Ccircle cx=\'50\' cy=\'50\' r=\'50\' fill=\'%23e91e63\'/%3E%3Ctext x=\'50\' y=\'60\' font-size=\'30\' text-anchor=\'middle\' fill=\'white\'%3EA%3C/text%3E%3C/svg%3E');">
                            </div>
                            <div class="avatar"
                                 style="background-image: url('data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 100 100\'%3E%3Ccircle cx=\'50\' cy=\'50\' r=\'50\' fill=\'%232196f3\'/%3E%3Ctext x=\'50\' y=\'60\' font-size=\'30\' text-anchor=\'middle\' fill=\'white\'%3EB%3C/text%3E%3C/svg%3E');">
                            </div>
                        </div>
                        <div class="date col-6 text-end">02 Jan 2025</div>
                    </div>
                </div>
                <div class="drop-area" data-position="1"></div>

                <div class="drag_card" draggable="true" data-card-id="5">
                    <div class="card-menu">⋮</div>
                    <div class="card-dropdown">
                        <div class="dropdown-item">Edit card</div>
                        <div class="dropdown-item delete">Delete card</div>
                    </div>
                    <div class="card-title">Create the app's wireframe</div>
                    <div class="priority-tag priority-medium">Medium</div>
                    <div class="progress-container">
                        <div class="progress-bar" style="width: 68%"></div>
                    </div>
                    <div class="card-meta">
                        <div class="card-meta-left">
                            <div class="meta-item">
                                <span><i class="fa-solid fa-paperclip fs-5"></i></span>
                                <span>2</span>
                            </div>
                            <div class="meta-item">
                                <span><i class="fa-regular fa-message fs-5"></i></span>
                                <span>2</span>
                            </div>
                        </div>
                    </div>
                    <div class="row align-items-end">
                        <div class="avatar-group col-6">
                            <div class="avatar"
                                 style="background-image: url('data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 100 100\'%3E%3Ccircle cx=\'50\' cy=\'50\' r=\'50\' fill=\'%23e91e63\'/%3E%3Ctext x=\'50\' y=\'60\' font-size=\'30\' text-anchor=\'middle\' fill=\'white\'%3EA%3C/text%3E%3C/svg%3E');">
                            </div>
                            <div class="avatar"
                                 style="background-image: url('data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 100 100\'%3E%3Ccircle cx=\'50\' cy=\'50\' r=\'50\' fill=\'%232196f3\'/%3E%3Ctext x=\'50\' y=\'60\' font-size=\'30\' text-anchor=\'middle\' fill=\'white\'%3EB%3C/text%3E%3C/svg%3E');">
                            </div>
                        </div>
                        <div class="date col-6 text-end">02 Jan 2025</div>
                    </div>
                </div>
                <div class="drop-area" data-position="2"></div>

                <div class="drag_card" draggable="true" data-card-id="6">
                    <div class="card-menu">⋮</div>
                    <div class="card-dropdown">
                        <div class="dropdown-item">Edit card</div>
                        <div class="dropdown-item delete">Delete card</div>
                    </div>
                    <div class="card-title">Create the app's wireframe</div>
                    <div class="priority-tag priority-critical">Critical</div>
                    <div class="progress-container">
                        <div class="progress-bar" style="width: 68%"></div>
                    </div>
                    <div class="card-meta">
                        <div class="card-meta-left">
                            <div class="meta-item">
                                <span><i class="fa-solid fa-paperclip fs-5"></i></span>
                                <span>2</span>
                            </div>
                            <div class="meta-item">
                                <span><i class="fa-regular fa-message fs-5"></i></span>
                                <span>2</span>
                            </div>
                        </div>
                    </div>
                    <div class="row align-items-end">
                        <div class="avatar-group col-6">
                            <div class="avatar"
                                 style="background-image: url('data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 100 100\'%3E%3Ccircle cx=\'50\' cy=\'50\' r=\'50\' fill=\'%23e91e63\'/%3E%3Ctext x=\'50\' y=\'60\' font-size=\'30\' text-anchor=\'middle\' fill=\'white\'%3EA%3C/text%3E%3C/svg%3E');">
                            </div>
                            <div class="avatar"
                                 style="background-image: url('data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 100 100\'%3E%3Ccircle cx=\'50\' cy=\'50\' r=\'50\' fill=\'%232196f3\'/%3E%3Ctext x=\'50\' y=\'60\' font-size=\'30\' text-anchor=\'middle\' fill=\'white\'%3EB%3C/text%3E%3C/svg%3E');">
                            </div>
                        </div>
                        <div class="date col-6 text-end">02 Jan 2025</div>
                    </div>
                </div>
                <div class="drop-area" data-position="3"></div>
            </div>
        </div>
        <div class="list" data-list-id="4">
            <div class="list-header">
                <div class="list-header-title">Done</div>
                <button class="add-button">+</button>
            </div>
            <div class="list-content" data-list-id="2">
                <div class="drop-area" data-position="0"></div>
                <div class="drag_card" draggable="true" data-card-id="4">
                    <div class="card-menu">⋮</div>
                    <div class="card-dropdown">
                        <div class="dropdown-item">Edit card</div>
                        <div class="dropdown-item delete">Delete card</div>
                    </div>
                    <div class="card-title">Application wireframe</div>
                    <div class="priority-tag priority-low">Low</div>
                    <div class="progress-container">
                        <div class="progress-bar" style="width: 68%"></div>
                    </div>
                    <div class="card-meta">
                        <div class="card-meta-left">
                            <div class="meta-item">
                                <span><i class="fa-solid fa-paperclip fs-5"></i></span>
                                <span>2</span>
                            </div>
                            <div class="meta-item">
                                <span><i class="fa-regular fa-message fs-5"></i></span>
                                <span>2</span>
                            </div>
                        </div>
                    </div>
                    <div class="row align-items-end">
                        <div class="avatar-group col-6">
                            <div class="avatar"
                                 style="background-image: url('data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 100 100\'%3E%3Ccircle cx=\'50\' cy=\'50\' r=\'50\' fill=\'%23e91e63\'/%3E%3Ctext x=\'50\' y=\'60\' font-size=\'30\' text-anchor=\'middle\' fill=\'white\'%3EA%3C/text%3E%3C/svg%3E');">
                            </div>
                            <div class="avatar"
                                 style="background-image: url('data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 100 100\'%3E%3Ccircle cx=\'50\' cy=\'50\' r=\'50\' fill=\'%232196f3\'/%3E%3Ctext x=\'50\' y=\'60\' font-size=\'30\' text-anchor=\'middle\' fill=\'white\'%3EB%3C/text%3E%3C/svg%3E');">
                            </div>
                        </div>
                        <div class="date col-6 text-end">02 Jan 2025</div>
                    </div>
                </div>
                <div class="drop-area" data-position="1"></div>

                <div class="drag_card" draggable="true" data-card-id="5">
                    <div class="card-menu">⋮</div>
                    <div class="card-dropdown">
                        <div class="dropdown-item">Edit card</div>
                        <div class="dropdown-item delete">Delete card</div>
                    </div>
                    <div class="card-title">Create the app's wireframe</div>
                    <div class="priority-tag priority-medium">Medium</div>
                    <div class="progress-container">
                        <div class="progress-bar" style="width: 68%"></div>
                    </div>
                    <div class="card-meta">
                        <div class="card-meta-left">
                            <div class="meta-item">
                                <span><i class="fa-solid fa-paperclip fs-5"></i></span>
                                <span>2</span>
                            </div>
                            <div class="meta-item">
                                <span><i class="fa-regular fa-message fs-5"></i></span>
                                <span>2</span>
                            </div>
                        </div>
                    </div>
                    <div class="row align-items-end">
                        <div class="avatar-group col-6">
                            <div class="avatar"
                                 style="background-image: url('data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 100 100\'%3E%3Ccircle cx=\'50\' cy=\'50\' r=\'50\' fill=\'%23e91e63\'/%3E%3Ctext x=\'50\' y=\'60\' font-size=\'30\' text-anchor=\'middle\' fill=\'white\'%3EA%3C/text%3E%3C/svg%3E');">
                            </div>
                            <div class="avatar"
                                 style="background-image: url('data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 100 100\'%3E%3Ccircle cx=\'50\' cy=\'50\' r=\'50\' fill=\'%232196f3\'/%3E%3Ctext x=\'50\' y=\'60\' font-size=\'30\' text-anchor=\'middle\' fill=\'white\'%3EB%3C/text%3E%3C/svg%3E');">
                            </div>
                        </div>
                        <div class="date col-6 text-end">02 Jan 2025</div>
                    </div>
                </div>
                <div class="drop-area" data-position="2"></div>

                <div class="drag_card" draggable="true" data-card-id="6">
                    <div class="card-menu">⋮</div>
                    <div class="card-dropdown">
                        <div class="dropdown-item">Edit card</div>
                        <div class="dropdown-item delete">Delete card</div>
                    </div>
                    <div class="card-title">Create the app's wireframe</div>
                    <div class="priority-tag priority-critical">Critical</div>
                    <div class="progress-container">
                        <div class="progress-bar" style="width: 68%"></div>
                    </div>
                    <div class="card-meta">
                        <div class="card-meta-left">
                            <div class="meta-item">
                                <span><i class="fa-solid fa-paperclip fs-5"></i></span>
                                <span>2</span>
                            </div>
                            <div class="meta-item">
                                <span><i class="fa-regular fa-message fs-5"></i></span>
                                <span>2</span>
                            </div>
                        </div>
                    </div>
                    <div class="row align-items-end">
                        <div class="avatar-group col-6">
                            <div class="avatar"
                                 style="background-image: url('data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 100 100\'%3E%3Ccircle cx=\'50\' cy=\'50\' r=\'50\' fill=\'%23e91e63\'/%3E%3Ctext x=\'50\' y=\'60\' font-size=\'30\' text-anchor=\'middle\' fill=\'white\'%3EA%3C/text%3E%3C/svg%3E');">
                            </div>
                            <div class="avatar"
                                 style="background-image: url('data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 100 100\'%3E%3Ccircle cx=\'50\' cy=\'50\' r=\'50\' fill=\'%232196f3\'/%3E%3Ctext x=\'50\' y=\'60\' font-size=\'30\' text-anchor=\'middle\' fill=\'white\'%3EB%3C/text%3E%3C/svg%3E');">
                            </div>
                        </div>
                        <div class="date col-6 text-end">02 Jan 2025</div>
                    </div>
                </div>
                <div class="drop-area" data-position="3"></div>
            </div>
        </div>
        <div class="list" data-list-id="5">
            <div class="list-header">
                <div class="list-header-title">Review</div>
                <button class="add-button">+</button>
            </div>
            <div class="list-content" data-list-id="2">
                <div class="drop-area" data-position="0"></div>
                <div class="drag_card" draggable="true" data-card-id="4">
                    <div class="card-menu">⋮</div>
                    <div class="card-dropdown">
                        <div class="dropdown-item">Edit card</div>
                        <div class="dropdown-item delete">Delete card</div>
                    </div>
                    <div class="card-title">Application wireframe</div>
                    <div class="priority-tag priority-low">Low</div>
                    <div class="progress-container">
                        <div class="progress-bar" style="width: 68%"></div>
                    </div>
                    <div class="card-meta">
                        <div class="card-meta-left">
                            <div class="meta-item">
                                <span><i class="fa-solid fa-paperclip fs-5"></i></span>
                                <span>2</span>
                            </div>
                            <div class="meta-item">
                                <span><i class="fa-regular fa-message fs-5"></i></span>
                                <span>2</span>
                            </div>
                        </div>
                    </div>
                    <div class="row align-items-end">
                        <div class="avatar-group col-6">
                            <div class="avatar"
                                 style="background-image: url('data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 100 100\'%3E%3Ccircle cx=\'50\' cy=\'50\' r=\'50\' fill=\'%23e91e63\'/%3E%3Ctext x=\'50\' y=\'60\' font-size=\'30\' text-anchor=\'middle\' fill=\'white\'%3EA%3C/text%3E%3C/svg%3E');">
                            </div>
                            <div class="avatar"
                                 style="background-image: url('data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 100 100\'%3E%3Ccircle cx=\'50\' cy=\'50\' r=\'50\' fill=\'%232196f3\'/%3E%3Ctext x=\'50\' y=\'60\' font-size=\'30\' text-anchor=\'middle\' fill=\'white\'%3EB%3C/text%3E%3C/svg%3E');">
                            </div>
                        </div>
                        <div class="date col-6 text-end">02 Jan 2025</div>
                    </div>
                </div>
                <div class="drop-area" data-position="1"></div>

                <div class="drag_card" draggable="true" data-card-id="5">
                    <div class="card-menu">⋮</div>
                    <div class="card-dropdown">
                        <div class="dropdown-item">Edit card</div>
                        <div class="dropdown-item delete">Delete card</div>
                    </div>
                    <div class="card-title">Create the app's wireframe</div>
                    <div class="priority-tag priority-medium">Medium</div>
                    <div class="progress-container">
                        <div class="progress-bar" style="width: 68%"></div>
                    </div>
                    <div class="card-meta">
                        <div class="card-meta-left">
                            <div class="meta-item">
                                <span><i class="fa-solid fa-paperclip fs-5"></i></span>
                                <span>2</span>
                            </div>
                            <div class="meta-item">
                                <span><i class="fa-regular fa-message fs-5"></i></span>
                                <span>2</span>
                            </div>
                        </div>
                    </div>
                    <div class="row align-items-end">
                        <div class="avatar-group col-6">
                            <div class="avatar"
                                 style="background-image: url('data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 100 100\'%3E%3Ccircle cx=\'50\' cy=\'50\' r=\'50\' fill=\'%23e91e63\'/%3E%3Ctext x=\'50\' y=\'60\' font-size=\'30\' text-anchor=\'middle\' fill=\'white\'%3EA%3C/text%3E%3C/svg%3E');">
                            </div>
                            <div class="avatar"
                                 style="background-image: url('data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 100 100\'%3E%3Ccircle cx=\'50\' cy=\'50\' r=\'50\' fill=\'%232196f3\'/%3E%3Ctext x=\'50\' y=\'60\' font-size=\'30\' text-anchor=\'middle\' fill=\'white\'%3EB%3C/text%3E%3C/svg%3E');">
                            </div>
                        </div>
                        <div class="date col-6 text-end">02 Jan 2025</div>
                    </div>
                </div>
                <div class="drop-area" data-position="2"></div>

                <div class="drag_card" draggable="true" data-card-id="6">
                    <div class="card-menu">⋮</div>
                    <div class="card-dropdown">
                        <div class="dropdown-item">Edit card</div>
                        <div class="dropdown-item delete">Delete card</div>
                    </div>
                    <div class="card-title">Create the app's wireframe</div>
                    <div class="priority-tag priority-critical">Critical</div>
                    <div class="progress-container">
                        <div class="progress-bar" style="width: 68%"></div>
                    </div>
                    <div class="card-meta">
                        <div class="card-meta-left">
                            <div class="meta-item">
                                <span><i class="fa-solid fa-paperclip fs-5"></i></span>
                                <span>2</span>
                            </div>
                            <div class="meta-item">
                                <span><i class="fa-regular fa-message fs-5"></i></span>
                                <span>2</span>
                            </div>
                        </div>
                    </div>
                    <div class="row align-items-end">
                        <div class="avatar-group col-6">
                            <div class="avatar"
                                 style="background-image: url('data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 100 100\'%3E%3Ccircle cx=\'50\' cy=\'50\' r=\'50\' fill=\'%23e91e63\'/%3E%3Ctext x=\'50\' y=\'60\' font-size=\'30\' text-anchor=\'middle\' fill=\'white\'%3EA%3C/text%3E%3C/svg%3E');">
                            </div>
                            <div class="avatar"
                                 style="background-image: url('data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 100 100\'%3E%3Ccircle cx=\'50\' cy=\'50\' r=\'50\' fill=\'%232196f3\'/%3E%3Ctext x=\'50\' y=\'60\' font-size=\'30\' text-anchor=\'middle\' fill=\'white\'%3EB%3C/text%3E%3C/svg%3E');">
                            </div>
                        </div>
                        <div class="date col-6 text-end">02 Jan 2025</div>
                    </div>
                </div>
                <div class="drop-area" data-position="3"></div>
            </div>
        </div>
        <div class="list-composer">
            <input type="text" placeholder="Enter list title...">
            <div class="composer-controls">
                <button class="add-btn">Add list</button>
                <button class="cancel-btn">Cancel</button>
            </div>
        </div>
    </div>


    <!-- Edit Template -->
    <div class="task-container d-none" id="taskContainer">
        <div class="task-card">
            <div class="task-stage-item">
                <div class="task-stage-left">
                    <div class="task-stage-icon">
                        <img src="{{ asset('assets/assestsnew/gridmenublue.svg') }}" alt="">
                    </div>
                    <div class="task-stage-title">To Do</div>
                </div>
                <div class="task-stage-actions">
                    <button class="task-action-btn task-edit-btn">
                        <img src="{{ asset('assets/assestsnew/edit.svg') }}" alt="">
                    </button>
                    <button class="task-action-btn task-delete-btn">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>


            <div class="task-stage-item">
                <div class="task-stage-left">
                    <div class="task-stage-icon">
                        <img src="{{ asset('assets/assestsnew/gridmenublue.svg') }}" alt="">
                    </div>
                    <div class="task-stage-title">In Progress</div>
                </div>
                <div class="task-stage-actions">
                    <button class="task-action-btn task-edit-btn">
                        <img src="{{ asset('assets/assestsnew/edit.svg') }}" alt="">
                    </button>
                    <button class="task-action-btn task-delete-btn">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>


            <div class="task-stage-item">
                <div class="task-stage-left">
                    <div class="task-stage-icon">
                        <img src="{{ asset('assets/assestsnew/gridmenublue.svg') }}" alt="">
                    </div>
                    <div class="task-stage-title">Review</div>
                </div>
                <div class="task-stage-actions">
                    <button class="task-action-btn task-edit-btn">
                        <img src="{{ asset('assets/assestsnew/edit.svg') }}" alt="">
                    </button>
                    <button class="task-action-btn task-delete-btn">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>


            <div class="task-stage-item">
                <div class="task-stage-left">
                    <div class="task-stage-icon">
                        <img src="{{ asset('assets/assestsnew/gridmenublue.svg') }}" alt="">
                    </div>
                    <div class="task-stage-title">Done</div>
                </div>
                <div class="task-stage-actions">
                    <button class="task-action-btn task-edit-btn">
                        <img src="{{ asset('assets/assestsnew/edit.svg') }}" alt="">
                    </button>
                    <button class="task-action-btn task-delete-btn">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>


            <div class="task-stage-item">
                <div class="task-stage-left">
                    <div class="task-stage-icon">
                        <img src="{{ asset('assets/assestsnew/gridmenublue.svg') }}" alt="">
                    </div>
                    <div class="task-stage-title">Trash</div>
                </div>
                <div class="task-stage-actions">
                    <button class="task-action-btn task-edit-btn">
                        <img src="{{ asset('assets/assestsnew/edit.svg') }}" alt="">
                    </button>
                    <button class="task-action-btn task-delete-btn">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>


            <div class="task-note">
                <span class="task-note-highlight">Note:</span> You can easily change order of project task
                stage using drag & drop.
            </div>

            <div class="task-buttons">
                <button class="task-cancel-btn">Cancel</button>
                <button class="task-save-btn">Save</button>
            </div>
        </div>
    </div>




    <!-- Model 1 -->

    <div class="modal fade" id="myModal" tabindex="1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow" style="border-radius: 30px; padding: 1rem; background-color: #fff;">
                <div class="modal-header border-0">
                    <h5 class="modal-title w-100 text-center" id="myModal">New Field</h5>
                    <button type="button" class="btn-close position-absolute end-0 me-3"
                            data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- Title -->
                    <div class="mb-3 w-100">
                        <label class="form-label fw-semibold">Title</label>
                        <input type="text" class="form-control" style="border-radius: 100px;"
                               placeholder="Add a title…">
                    </div>

                    <!-- Type -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Type</label>
                        <select class="form-select" style="max-width: 100%;">
                            <option>Dropdown</option>
                            <option>Checkbox</option>
                            <option>Date</option>
                            <option>Number</option>
                            <option>Text</option>
                        </select>
                    </div>

                    <!-- Options -->
                    <div class="row align-items-end g-2 mb-3">
                        <div class="col-9">
                            <label class="form-label fw-semibold">Options</label>
                            <input type="text" class="form-control" style="border-radius: 100px;"
                                   placeholder="Add item…">
                        </div>
                        <div class="col-3 d-flex align-items-end">
                            <button class="btn btn-outline-primary w-100" style="border-radius: 100px;">Add</button>
                        </div>
                    </div>

                    <!-- Create Button -->
                    <button class="btn btn-primary w-100 mt-2" style="border-radius: 100px;">Create</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Model 2 -->
    <div class="modal fade" id="selectMembersModal" tabindex="1" aria-labelledby="selectMembersLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow-lg" style="border-radius: 20px;">

                <!-- Modal Header -->
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold" id="selectMembersLabel">Select Members</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <!-- Modal Body -->
                <div class="modal-body px-3 pt-0">

                    <!-- Search Input -->
                    <div class="position-relative w-100">
                        <input type="text" class="form-control rounded-pill ps-4 pe-5" placeholder="Search Members.."/>
                        <span class="position-absolute end-0 top-50 translate-middle-y pe-3">
            <i class="fa fa-search" style="color: #316FF6;"></i>
          </span>
                    </div>

                    <!-- Card Members -->
                    <p class="fw-semibold mt-3 fs-5">Card Members</p>
                    <div class="member-item d-flex align-items-center gap-2 mb-2">
                        <img src="https://randomuser.me/api/portraits/men/10.jpg" alt="" width="40"
                             class="rounded-circle"/>
                        <span>Arun Kumar</span>
                    </div>
                    <div class="member-item d-flex align-items-center gap-2 mb-2">
                        <img src="https://randomuser.me/api/portraits/women/20.jpg" alt="" width="40"
                             class="rounded-circle"/>
                        <span>Anu Sri</span>
                    </div>

                    <!-- Board Members -->
                    <p class="fw-semibold mt-3 fs-5">Board Members</p>
                    <div class="member-item d-flex align-items-center gap-2 mb-2">
                        <img src="https://randomuser.me/api/portraits/men/30.jpg" alt="" width="40"
                             class="rounded-circle"/>
                        <span>Arun Kumar</span>
                    </div>
                    <div class="member-item d-flex align-items-center gap-2 mb-2">
                        <img src="https://randomuser.me/api/portraits/women/31.jpg" alt="" width="40"
                             class="rounded-circle"/>
                        <span>Anu Sri</span>
                    </div>
                    <div class="member-item d-flex align-items-center gap-2 mb-2">
                        <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="" width="40"
                             class="rounded-circle"/>
                        <span>Arun Kumar</span>
                    </div>
                    <div class="member-item d-flex align-items-center gap-2 mb-2">
                        <img src="https://randomuser.me/api/portraits/women/33.jpg" alt="" width="40"
                             class="rounded-circle"/>
                        <span>Anu Sri</span>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Model 3 -->
    <div class="modal fade" id="dateModal" tabindex="-1" aria-labelledby="dateModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 shadow border-0">

                <!-- Modal Body -->
                <div class="modal-body p-4">

                    <!-- Header -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold mb-0" id="dateModalLabel">Dates</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <!-- Calendar Section -->
                    <div class="container mt-2">
                        <div class=" mb-2" id="calendarContainer">
                            <!-- Calendar will be inserted here -->
                        </div>
                    </div>

                    <!-- Start Date -->
                    <div class="mb-3">
                        <label class="fw-medium fs-5 text-black mb-2 d-block" for="startDateCheck">Start date</label>
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded d-flex align-items-center justify-content-center">
                                <input class="form-check-input m-0" type="checkbox" id="startDateCheck"
                                       onchange="toggleStartDate(this)"
                                       style="width: 16px; height: 16px;">
                            </div>
                            <input type="date" id="startDateInput" disabled
                                   style="background-color: #f4f4f4; border: 1px solid #2962ff; border-radius: 6px; padding: 6px 10px; width: 120px; font-size: 14px;"/>
                        </div>
                    </div>

                    <!-- Due Date -->
                    <div class="mb-3">
                        <label class="fw-medium fs-5 text-black mb-2 d-block" for="dueDateCheck">Due date</label>
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded d-flex align-items-center justify-content-center">
                                <input class="form-check-input m-0" type="checkbox" id="dueDateCheck" checked
                                       style="accent-color: white; width: 16px; height: 16px;">
                            </div>
                            <input type="date" value="2021-04-07"
                                   style="background-color: #f4f4f4; border: 1px solid #2962ff; border-radius: 6px; padding: 6px 10px; width: 120px; font-size: 14px;"/>
                            <input type="time" value="15:00"
                                   style="background-color: #f4f4f4; border: 1px solid #2962ff; border-radius: 6px; padding: 6px 10px; width: 100px; font-size: 14px;"/>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-grid gap-3 mt-4">
                        <button class="btn btn-primary rounded-pill">Save</button>
                        <button class="btn btn-outline-secondary rounded-pill">Remove</button>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

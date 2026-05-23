<?php

use App\Http\Controllers\PublicPageController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicPageController::class, 'home'])->name('home');
Route::get('/courses', [PublicPageController::class, 'courses'])->name('courses.index');
Route::get('/courses/{course:slug}', [PublicPageController::class, 'course'])->name('course.show');
Route::get('/courses/{course:slug}/theory', [PublicPageController::class, 'courseTheory'])->name('course.theory');
Route::get('/courses/{course:slug}/practice', [PublicPageController::class, 'coursePractice'])->name('course.practice');
Route::get('/courses/{course:slug}/chapters/{chapter:slug}', [PublicPageController::class, 'chapter'])->name('chapter.show');
Route::get('/courses/{course:slug}/chapters/{chapter:slug}/theory', [PublicPageController::class, 'chapterTheory'])->name('chapter.theory');
Route::get('/courses/{course:slug}/chapters/{chapter:slug}/practice', [PublicPageController::class, 'chapterPractice'])->name('chapter.practice');
Route::get('/chapters/{chapter:slug}', [PublicPageController::class, 'chapterBySlug'])->name('chapter.show.simple');
Route::get('/chapters/{chapter:slug}/theory', [PublicPageController::class, 'chapterTheoryBySlug'])->name('chapter.theory.simple');
Route::get('/chapters/{chapter:slug}/practice', [PublicPageController::class, 'chapterPracticeBySlug'])->name('chapter.practice.simple');
Route::get('/problems/{problem:problem_code}', [PublicPageController::class, 'problem'])->name('problem.show');
Route::get('/ladders', [PublicPageController::class, 'ladders'])->name('ladders.index');
Route::get('/ladders/{ladder:slug}', [PublicPageController::class, 'ladder'])->name('ladders.show');
Route::get('/ladders/{ladder:slug}/practice', [PublicPageController::class, 'ladderPractice'])->name('ladders.practice');

Route::post('/problems/{problem:problem_code}/bookmark-toggle', [PublicPageController::class, 'toggleBookmark'])->name('problem.bookmark.toggle');
Route::post('/problems/{problem:problem_code}/solved-toggle', [PublicPageController::class, 'toggleSolved'])->name('problem.solved.toggle');

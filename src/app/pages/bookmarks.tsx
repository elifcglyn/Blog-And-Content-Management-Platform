import * as React from "react";
import { Bookmark, Search, ArrowRight, BookOpen } from "lucide-react";
import { mockPosts } from "../data/mock-data";
import { Link } from "react-router";

export function BookmarksPage() {
  // Simülasyon: İlk 2 yazıyı kaydedilmiş gibi gösterelim
  const savedPosts = mockPosts.slice(0, 2);

  return (
    <div className="flex-1 bg-white dark:bg-slate-950 p-8 md:p-12 animate-in slide-in-from-right-4 duration-700">
      <div className="max-w-4xl mx-auto">
        <header className="flex justify-between items-end mb-16 pb-8 border-b border-slate-100 dark:border-slate-800">
          <div>
            <div className="flex items-center gap-2 text-teal-600 font-bold text-[10px] uppercase tracking-[0.3em] mb-4">
                <Bookmark className="w-4 h-4 fill-current" /> <span>Kütüphanen</span>
            </div>
            <h1 className="font-serif text-5xl md:text-6xl italic tracking-tighter text-slate-900 dark:text-white">Kaydedilenler</h1>
          </div>
          <p className="text-slate-400 italic text-sm">{savedPosts.length} Hikaye</p>
        </header>

        {savedPosts.length > 0 ? (
          <div className="space-y-12">
            {savedPosts.map((post) => (
              <Link 
                key={post.id} 
                to={`/post/${post.slug}`}
                className="group flex flex-col md:flex-row gap-8 items-center"
              >
                <div className="w-full md:w-64 aspect-video rounded-3xl overflow-hidden shadow-lg group-hover:scale-105 transition-transform duration-500">
                    <img src={post.coverImage} className="w-full h-full object-cover" />
                </div>
                <div className="flex-1 space-y-3 text-center md:text-left">
                    <h2 className="font-serif text-3xl italic text-slate-900 dark:text-white group-hover:text-teal-600 transition-colors leading-tight">
                        {post.title}
                    </h2>
                    <p className="text-slate-500 text-sm line-clamp-2 italic font-light">{post.excerpt}</p>
                    <div className="flex items-center justify-center md:justify-start gap-4 pt-2">
                        <span className="text-[10px] font-bold uppercase tracking-widest text-slate-400">{post.author.name}</span>
                        <span className="w-1 h-1 bg-slate-300 rounded-full"></span>
                        <span className="text-[10px] font-bold uppercase tracking-widest text-slate-400">{post.readingTime} dk okuma</span>
                    </div>
                </div>
                <ArrowRight className="hidden md:block w-6 h-6 text-slate-200 group-hover:text-teal-600 group-hover:translate-x-2 transition-all" />
              </Link>
            ))}
          </div>
        ) : (
          <div className="py-20 text-center space-y-6">
            <div className="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto">
                <BookOpen className="w-10 h-10 text-slate-200" />
            </div>
            <p className="font-serif italic text-2xl text-slate-400">Henüz bir şey kaydetmedin...</p>
            <Link to="/" className="inline-block text-teal-600 font-bold border-b-2 border-teal-600 pb-1">Hikayeleri Keşfet</Link>
          </div>
        )}
      </div>
    </div>
  );
}
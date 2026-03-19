import * as React from "react";
import { Link } from "react-router";
import { Sparkles, TrendingUp, Clock, ChevronRight } from "lucide-react";
import { mockPosts } from "../data/mock-data";
import { PostCard } from "../components/post-card";

import { BookmarkButton } from "../components/bookmark-button";
export function HomePage() {
  // Yükleme ekranı artık yok, direkt verileri alıyoruz
  const featuredPost = mockPosts[0];
  const remainingPosts = mockPosts.slice(1);

  return (
    <div className="flex-1 flex flex-col bg-white dark:bg-slate-950 font-sans overflow-auto animate-in fade-in duration-700">
      
      {/* 🚀 TOPBAR (HEADER) GERİ GELDİ */}
      
      
      <main className="container mx-auto max-w-7xl py-12 px-6">
        
        {/* HAFTANIN HİKAYESİ (Featured) */}
        <section className="mb-20">
          <div className="flex items-center gap-2 text-teal-600 font-bold text-xs uppercase tracking-[0.2em] mb-6">
            <Sparkles className="w-4 h-4" /> <span>Haftanın Hikayesi</span>
          </div>
          <BookmarkButton />
          <Link 
            to={`/post/${featuredPost.slug}`}
            className="group cursor-pointer grid grid-cols-1 lg:grid-cols-12 gap-10 items-center"
          >
            <div className="lg:col-span-7 rounded-[3rem] overflow-hidden shadow-2xl shadow-teal-500/5 aspect-[16/9] relative">
              <img 
                src={featuredPost.coverImage} 
                alt={featuredPost.title}
                className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700" 
              />
              <div className="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent" />
            </div>
            
            <div className="lg:col-span-5 space-y-6">
              <h2 className="font-serif text-5xl md:text-6xl italic leading-[1.1] tracking-tighter text-slate-900 dark:text-white group-hover:text-teal-600 transition-colors">
                {featuredPost.title}
              </h2>
              <p className="text-slate-500 text-lg leading-relaxed font-light line-clamp-3 italic">
                {featuredPost.excerpt}
              </p>
              <div className="flex items-center gap-4 pt-4 border-t border-slate-100 w-fit">
                <span className="text-sm font-bold uppercase tracking-widest text-slate-900 dark:text-white flex items-center gap-2">
                  Devamını Oku <ChevronRight className="w-4 h-4" />
                </span>
              </div>
            </div>
          </Link>
        </section>

        {/* DİĞER HİKAYELER */}
        <section>
          <div className="flex items-center justify-between mb-10 border-b border-slate-100 pb-6">
            <div className="flex items-center gap-2 text-slate-900 dark:text-white font-bold text-sm uppercase tracking-widest">
              <TrendingUp className="w-5 h-5 text-teal-600" /> <span>Popüler Akış</span>
            </div>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-10 gap-y-16">
            {remainingPosts.map((post) => (
              <Link key={post.id} to={`/post/${post.slug}`} className="transition-transform hover:-translate-y-1 duration-300">
                <PostCard {...post} />
              </Link>
            ))}
          </div>
        </section>
      </main>
    </div>
  );
}